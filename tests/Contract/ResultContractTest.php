<?php

declare(strict_types=1);

use MegSEO\DTO\AnalysisResult;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;
use MegSEO\Core\Engine;
use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;

test('AnalysisResult preserves deterministic ordering across identical runs', function () {
    $engine = Engine::make();

    $order = ['check.z', 'check.a', 'check.m'];

    foreach ($order as $id) {
        $engine->register(new class($id) implements Check
        {
            private readonly string $id;

            public function __construct(string $id) { $this->id = $id; }

            public function ref(): CheckReference
            {
                return new CheckReference($this->id, "Check {$this->id}");
            }

            public function analyze(AnalysisContext $context): CheckOutcome
            {
                return new CheckOutcome(
                    check: $this->ref(),
                    issues: [new AnalysisIssue("Issue {$this->id}", 'details', $this->id)],
                    warnings: [new AnalysisWarning("Warning {$this->id}", 'details', $this->id)],
                    suggestions: [new AnalysisSuggestion("Suggestion {$this->id}", 'details', $this->id)],
                );
            }
        });
    }

    $r1 = $engine->analyze(new AnalysisContext('test'));
    $r2 = $engine->analyze(new AnalysisContext('test'));

    $ids1 = array_map(fn ($i) => $i->sourceCheckId, $r1->issues());
    $ids2 = array_map(fn ($i) => $i->sourceCheckId, $r2->issues());

    expect($ids1)->toBe($ids2);
    expect($ids1)->toBe($order);
});

test('AnalysisResult accessors return expected types', function () {
    $score = new ScoreSummary(value: 75.0);
    $issues = [new AnalysisIssue('i', 'd', 'check.a')];
    $warnings = [new AnalysisWarning('w', 'd', 'check.a')];
    $suggestions = [new AnalysisSuggestion('s', 'd', 'check.a')];

    $result = new AnalysisResult(
        score: $score,
        issues: $issues,
        warnings: $warnings,
        suggestions: $suggestions,
    );

    expect($result->score())->toBeInstanceOf(ScoreSummary::class);
    expect($result->issues())->toBeArray();
    expect($result->warnings())->toBeArray();
    expect($result->suggestions())->toBeArray();
});

test('AnalysisResult holds failure metadata for isolated failures', function () {
    $ref = new CheckReference('fail.a', 'Failing');
    $score = new ScoreSummary();

    $result = new AnalysisResult(
        score: $score,
        failures: [
            ['check' => $ref, 'error' => 'Something went wrong'],
        ],
    );

    expect($result->failures)->toHaveCount(1);
    expect($result->failures[0]['check'])->toBe($ref);
    expect($result->failures[0]['error'])->toBe('Something went wrong');
});

test('AnalysisResult toArray preserves failure metadata', function () {
    $ref = new CheckReference('fail.b', 'Failing B');
    $score = new ScoreSummary();
    $issues = [new AnalysisIssue('Problem', 'Details', 'fail.b')];

    $result = new AnalysisResult(
        score: $score,
        issues: $issues,
        failures: [['check' => $ref, 'error' => 'Boom']],
    );

    $array = $result->toArray();

    expect($array['issues'])->toHaveCount(1);
    expect($array['failures'])->toHaveCount(1);
    expect($array['failures'][0])->toMatchArray([
        'check' => 'fail.b',
        'error' => 'Boom',
    ]);
});

test('empty AnalysisResult produces valid empty toArray', function () {
    $result = new AnalysisResult(score: new ScoreSummary());
    $array = $result->toArray();

    expect($array)->toMatchArray([
        'score' => ['value' => null, 'contributors' => []],
        'issues' => [],
        'warnings' => [],
        'suggestions' => [],
        'failures' => [],
        'metadata' => [],
    ]);
});

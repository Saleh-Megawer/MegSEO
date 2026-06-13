<?php

declare(strict_types=1);

use MegSEO\Contracts\Check;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;

function makeIssueCheck(string $id, string $label): Check
{
    return new class($id, $label) implements Check
    {
        private readonly string $id;
        private readonly string $label;

        public function __construct(string $id, string $label)
        {
            $this->id = $id;
            $this->label = $label;
        }

        public function ref(): CheckReference
        {
            return new CheckReference($this->id, $this->label);
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                issues: [new AnalysisIssue('Issue from ' . $this->id, 'Details', $this->id)],
            );
        }
    };
}

function makeScoreCheck(string $id, string $label, float $value): Check
{
    return new class($id, $label, $value) implements Check
    {
        private readonly string $id;
        private readonly string $label;
        private readonly float $value;

        public function __construct(string $id, string $label, float $value)
        {
            $this->id = $id;
            $this->label = $label;
            $this->value = $value;
        }

        public function ref(): CheckReference
        {
            return new CheckReference($this->id, $this->label);
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                scoreContribution: new ScoreSummary(value: $this->value),
            );
        }
    };
}

function makeEmptyCheck(string $id, string $label): Check
{
    return new class($id, $label) implements Check
    {
        private readonly string $id;
        private readonly string $label;

        public function __construct(string $id, string $label)
        {
            $this->id = $id;
            $this->label = $label;
        }

        public function ref(): CheckReference
        {
            return new CheckReference($this->id, $this->label);
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };
}

test('new check registered alongside existing checks participates in analysis', function () {
    $engine = Engine::make();

    $engine->register(makeIssueCheck('check.pre', 'Pre-existing'));

    $result1 = $engine->analyze(new AnalysisContext('test'));
    expect($result1->issues())->toHaveCount(1);

    $engine->register(makeScoreCheck('check.new', 'New Check', 50.0));

    $result2 = $engine->analyze(new AnalysisContext('test'));

    expect($result2->issues())->toHaveCount(1);
    expect($result2->score()->value)->toBe(50.0);
    expect($result2->issues()[0]->sourceCheckId)->toBe('check.pre');
});

test('existing check behavior is unchanged after adding new checks', function () {
    $engine = Engine::make();

    $engine->register(makeIssueCheck('check.existing', 'Existing'));

    $resultBefore = $engine->analyze(new AnalysisContext('test'));
    $issueCountBefore = count($resultBefore->issues());

    $engine->register(makeScoreCheck('check.extra', 'Extra', 30.0));
    $engine->register(makeEmptyCheck('check.empty', 'Empty'));

    $resultAfter = $engine->analyze(new AnalysisContext('test'));

    $existingIssues = array_filter(
        $resultAfter->issues(),
        fn ($i) => $i->sourceCheckId === 'check.existing',
    );
    expect(count($existingIssues))->toBe($issueCountBefore);
});

test('deterministic ordering preserves registration sequence with mixed checks', function () {
    $engine = Engine::make();

    $registrationOrder = ['check.third', 'check.first', 'check.second'];

    foreach ($registrationOrder as $id) {
        $engine->register(makeIssueCheck($id, "Check {$id}"));
    }

    $result1 = $engine->analyze(new AnalysisContext('test'));
    $result2 = $engine->analyze(new AnalysisContext('test'));

    $order1 = array_map(fn ($i) => $i->sourceCheckId, $result1->issues());
    $order2 = array_map(fn ($i) => $i->sourceCheckId, $result2->issues());

    expect($order1)->toBe($order2);
    expect($order1)->toBe($registrationOrder);
});

test('checks registered after first analysis execute on next analysis', function () {
    $engine = Engine::make();

    $engine->register(makeEmptyCheck('check.first', 'First'));

    $result1 = $engine->analyze(new AnalysisContext('test'));
    expect($result1->issues())->toBeEmpty();

    $engine->register(makeIssueCheck('check.second', 'Second'));

    $result2 = $engine->analyze(new AnalysisContext('test'));
    expect($result2->issues())->toHaveCount(1);
    expect($result2->issues()[0]->sourceCheckId)->toBe('check.second');
});

test('multiple independent checks from different feature modules coexist', function () {
    $engine = Engine::make();

    $engine->register(makeIssueCheck('module_a.title', 'Title Check'));
    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('module_b.meta', 'Meta Check');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                scoreContribution: new ScoreSummary(value: 60.0),
                issues: [new AnalysisIssue('Meta issue', 'details', 'module_b.meta')],
            );
        }
    });

    $result = $engine->analyze(new AnalysisContext('test'));

    expect($result->issues())->toHaveCount(2);
    expect($result->score()->value)->toBe(60.0);

    $sourceIds = array_map(fn ($i) => $i->sourceCheckId, $result->issues());
    expect($sourceIds)->toContain('module_a.title');
    expect($sourceIds)->toContain('module_b.meta');
});

test('check identifiers are preserved in output for traceability', function () {
    $engine = Engine::make();

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('trace.me', 'Traceable', '2.0.0');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                issues: [new AnalysisIssue('Traceable issue', 'details', 'trace.me')],
            );
        }
    });

    $result = $engine->analyze(new AnalysisContext('test'));

    $issue = $result->issues()[0];
    expect($issue->sourceCheckId)->toBe('trace.me');
    expect($result->issues()[0]->sourceCheckId)->toBe('trace.me');
});

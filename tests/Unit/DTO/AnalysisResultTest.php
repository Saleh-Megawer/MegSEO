<?php

declare(strict_types=1);

use MegSEO\DTO\AnalysisResult;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\ScoreSummary;

test('AnalysisResult exposes required accessors', function () {
    $score = new ScoreSummary(value: 85.0);
    $issues = [new AnalysisIssue('Issue', 'Details', 'check.a')];
    $warnings = [new AnalysisWarning('Warning', 'Details', 'check.a')];
    $suggestions = [new AnalysisSuggestion('Suggestion', 'Details', 'check.a')];

    $result = new AnalysisResult(
        score: $score,
        issues: $issues,
        warnings: $warnings,
        suggestions: $suggestions,
        metadata: ['analyzed_at' => '2026-06-13'],
    );

    expect($result->score())->toBe($score);
    expect($result->issues())->toBe($issues);
    expect($result->warnings())->toBe($warnings);
    expect($result->suggestions())->toBe($suggestions);
});

test('AnalysisResult is valid for empty pipeline', function () {
    $result = new AnalysisResult(score: new ScoreSummary());

    expect($result->issues())->toBe([]);
    expect($result->warnings())->toBe([]);
    expect($result->suggestions())->toBe([]);
    expect($result->failures)->toBe([]);
    expect($result->metadata)->toBe([]);
});

test('AnalysisResult can hold failures from isolated checks', function () {
    $ref = new MegSEO\DTO\CheckReference('check.fail', 'Failing Check');
    $score = new ScoreSummary();

    $result = new AnalysisResult(
        score: $score,
        failures: [
            ['check' => $ref, 'error' => 'Check threw RuntimeException'],
        ],
    );

    expect($result->failures)->toHaveCount(1);
    expect($result->failures[0]['check']->id)->toBe('check.fail');
    expect($result->failures[0]['error'])->toBe('Check threw RuntimeException');
});

test('AnalysisResult preserves deterministic issue ordering', function () {
    $issues = [
        new AnalysisIssue('First', 'd1', 'check.a'),
        new AnalysisIssue('Second', 'd2', 'check.b'),
        new AnalysisIssue('Third', 'd3', 'check.a'),
    ];

    $result = new AnalysisResult(score: new ScoreSummary(), issues: $issues);

    expect($result->issues())->toHaveCount(3);
    expect($result->issues()[0]->message)->toBe('First');
    expect($result->issues()[1]->message)->toBe('Second');
    expect($result->issues()[2]->message)->toBe('Third');
});

<?php

declare(strict_types=1);

use MegSEO\Support\DefaultCheckResultFactory;
use MegSEO\Support\DefaultContextFactory;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\ScoreSummary;
use MegSEO\DTO\AnalysisResult;
use MegSEO\Contracts\ArrayableResult;

test('DefaultCheckResultFactory creates CheckOutcome with score', function () {
    $factory = new DefaultCheckResultFactory();

    $outcome = $factory->createOutcome('check.id', 75.0);

    expect($outcome)->toBeInstanceOf(CheckOutcome::class);
    expect($outcome->check->id)->toBe('check.id');
    expect($outcome->scoreContribution)->toBeInstanceOf(ScoreSummary::class);
    expect($outcome->scoreContribution->value)->toBe(75.0);
});

test('DefaultCheckResultFactory creates CheckOutcome without score', function () {
    $factory = new DefaultCheckResultFactory();

    $outcome = $factory->createOutcome('check.null');

    expect($outcome->scoreContribution)->toBeNull();
});

test('DefaultCheckResultFactory includes all finding types', function () {
    $factory = new DefaultCheckResultFactory();

    $outcome = $factory->createOutcome(
        'check.full',
        80.0,
        issues: [new AnalysisIssue('Issue', 'Detail', 'check.full')],
        warnings: [new AnalysisWarning('Warning', 'Detail', 'check.full')],
        suggestions: [new AnalysisSuggestion('Suggestion', 'Detail', 'check.full')],
        metadata: ['key' => 'value'],
    );

    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->warnings)->toHaveCount(1);
    expect($outcome->suggestions)->toHaveCount(1);
    expect($outcome->metadata)->toBe(['key' => 'value']);
});

test('DefaultContextFactory creates AnalysisContext with defaults', function () {
    $factory = new DefaultContextFactory();

    $ctx = $factory->create('subject');

    expect($ctx)->toBeInstanceOf(AnalysisContext::class);
    expect($ctx->subject)->toBe('subject');
    expect($ctx->attributes->count())->toBe(0);
    expect($ctx->options)->toBe([]);
    expect($ctx->requestId)->toBeNull();
});

test('DefaultContextFactory creates AnalysisContext with all fields', function () {
    $factory = new DefaultContextFactory();

    $ctx = $factory->create(
        'subject',
        attributes: ['lang' => 'en'],
        options: ['verbose' => true],
        requestId: 'r-42',
    );

    expect($ctx->attributes->get('lang'))->toBe('en');
    expect($ctx->options)->toBe(['verbose' => true]);
    expect($ctx->requestId)->toBe('r-42');
});

test('AnalysisResult implements ArrayableResult', function () {
    $score = new ScoreSummary(value: 85.0);
    $result = new AnalysisResult(score: $score);

    expect($result)->toBeInstanceOf(ArrayableResult::class);
});

test('AnalysisResult::toArray() produces deterministic structure', function () {
    $score = new ScoreSummary(value: 90.0, contributors: [
        ['value' => 90.0, 'sourceCheckId' => 'check.a'],
    ]);
    $issues = [new AnalysisIssue('Issue', 'Details', 'check.a', 0.9)];
    $warnings = [new AnalysisWarning('Warning', 'Details', 'check.a')];
    $suggestions = [new AnalysisSuggestion('Suggestion', 'Details', 'check.a', 0.8)];

    $result = new AnalysisResult(
        score: $score,
        issues: $issues,
        warnings: $warnings,
        suggestions: $suggestions,
        metadata: ['analyzed_at' => '2026-06-13'],
    );

    $array = $result->toArray();

    expect($array)->toBeArray();
    expect($array['score'])->toBeArray();
    expect($array['score']['value'])->toBe(90.0);
    expect($array['score']['contributors'])->toHaveCount(1);

    expect($array['issues'])->toHaveCount(1);
    expect($array['issues'][0])->toMatchArray([
        'message' => 'Issue',
        'details' => 'Details',
        'sourceCheckId' => 'check.a',
        'confidence' => 0.9,
    ]);

    expect($array['warnings'])->toHaveCount(1);
    expect($array['warnings'][0]['sourceCheckId'])->toBe('check.a');

    expect($array['suggestions'])->toHaveCount(1);
    expect($array['suggestions'][0]['sourceCheckId'])->toBe('check.a');

    expect($array['metadata'])->toBe(['analyzed_at' => '2026-06-13']);
});

test('AnalysisResult::toArray() handles failures', function () {
    $ref = new MegSEO\DTO\CheckReference('fail.check', 'Failing');
    $score = new ScoreSummary();

    $result = new AnalysisResult(
        score: $score,
        failures: [['check' => $ref, 'error' => 'Runtime error']],
    );

    $array = $result->toArray();

    expect($array['failures'])->toHaveCount(1);
    expect($array['failures'][0])->toMatchArray([
        'check' => 'fail.check',
        'error' => 'Runtime error',
    ]);
});

test('AnalysisResult::toArray() returns empty arrays for empty result', function () {
    $result = new AnalysisResult(score: new ScoreSummary());
    $array = $result->toArray();

    expect($array['issues'])->toBe([]);
    expect($array['warnings'])->toBe([]);
    expect($array['suggestions'])->toBe([]);
    expect($array['failures'])->toBe([]);
    expect($array['metadata'])->toBe([]);
});

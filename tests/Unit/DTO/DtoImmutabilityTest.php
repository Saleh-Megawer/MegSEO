<?php

declare(strict_types=1);

use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ExecutionDecision;
use MegSEO\DTO\ScoreSummary;

test('AnalysisContext is immutable via readonly properties', function () {
    $ctx = new AnalysisContext(
        subject: 'test-subject',
        attributes: ['key' => 'value'],
        options: ['opt' => true],
        requestId: 'req-1',
    );

    expect($ctx->subject)->toBe('test-subject');
    expect($ctx->attributes->get('key'))->toBe('value');
    expect($ctx->options)->toBe(['opt' => true]);
    expect($ctx->requestId)->toBe('req-1');
});

test('AnalysisContext with default values', function () {
    $ctx = new AnalysisContext('subject');

    expect($ctx->subject)->toBe('subject');
    expect($ctx->attributes->count())->toBe(0);
    expect($ctx->options)->toBe([]);
    expect($ctx->requestId)->toBeNull();
});

test('CheckReference exposes stable identity', function () {
    $ref = new CheckReference(
        id: 'check.title',
        label: 'Title Check',
        version: '1.0.0',
    );

    expect($ref->id)->toBe('check.title');
    expect($ref->label)->toBe('Title Check');
    expect($ref->version)->toBe('1.0.0');
});

test('CheckReference without version', function () {
    $ref = new CheckReference('check.id', 'Check Label');

    expect($ref->version)->toBeNull();
});

test('ScoreSummary with value and contributors', function () {
    $contributors = [
        ['value' => 10.5, 'sourceCheckId' => 'check.a'],
        ['value' => 5.0, 'sourceCheckId' => 'check.b'],
    ];

    $score = new ScoreSummary(
        value: 15.5,
        contributors: $contributors,
        metadata: ['normalized' => true],
    );

    expect($score->value)->toBe(15.5);
    expect($score->contributors)->toBe($contributors);
    expect($score->metadata)->toBe(['normalized' => true]);
});

test('ScoreSummary with null value for no scoring data', function () {
    $score = new ScoreSummary();

    expect($score->value)->toBeNull();
    expect($score->contributors)->toBe([]);
    expect($score->metadata)->toBe([]);
});

test('ExecutionDecision with continue action', function () {
    $decision = new ExecutionDecision(
        action: 'continue',
        reason: 'Non-critical check failure',
        recordFailure: true,
    );

    expect($decision->action)->toBe('continue');
    expect($decision->reason)->toBe('Non-critical check failure');
    expect($decision->recordFailure)->toBeTrue();
});

test('ExecutionDecision with abort action', function () {
    $decision = new ExecutionDecision(
        action: 'abort',
        reason: 'Critical failure',
        recordFailure: false,
    );

    expect($decision->action)->toBe('abort');
    expect($decision->recordFailure)->toBeFalse();
});

test('CheckOutcome with full findings', function () {
    $ref = new CheckReference('check.a', 'Check A');
    $score = new ScoreSummary(value: 100.0);
    $issues = [new AnalysisIssue('Issue', 'Details', 'check.a')];
    $warnings = [new AnalysisWarning('Warning', 'Details', 'check.a')];
    $suggestions = [new AnalysisSuggestion('Suggestion', 'Details', 'check.a')];

    $outcome = new CheckOutcome(
        check: $ref,
        scoreContribution: $score,
        issues: $issues,
        warnings: $warnings,
        suggestions: $suggestions,
        metadata: ['runtime' => 'fast'],
    );

    expect($outcome->check)->toBe($ref);
    expect($outcome->scoreContribution)->toBe($score);
    expect($outcome->issues)->toBe($issues);
    expect($outcome->warnings)->toBe($warnings);
    expect($outcome->suggestions)->toBe($suggestions);
    expect($outcome->metadata)->toBe(['runtime' => 'fast']);
});

test('CheckOutcome with empty defaults', function () {
    $ref = new CheckReference('check.b', 'Check B');
    $outcome = new CheckOutcome(check: $ref);

    expect($outcome->scoreContribution)->toBeNull();
    expect($outcome->issues)->toBe([]);
    expect($outcome->warnings)->toBe([]);
    expect($outcome->suggestions)->toBe([]);
    expect($outcome->metadata)->toBe([]);
});

test('AnalysisIssue with optional confidence', function () {
    $issue = new AnalysisIssue('Broken', 'More info', 'check.id', 0.95);

    expect($issue->message)->toBe('Broken');
    expect($issue->details)->toBe('More info');
    expect($issue->sourceCheckId)->toBe('check.id');
    expect($issue->confidence)->toBe(0.95);
});

test('AnalysisIssue without confidence', function () {
    $issue = new AnalysisIssue('Broken', 'More info', 'check.id');

    expect($issue->confidence)->toBeNull();
});

test('AnalysisWarning holds required fields', function () {
    $warning = new AnalysisWarning('Warning', 'Details', 'check.id');

    expect($warning->message)->toBe('Warning');
    expect($warning->details)->toBe('Details');
    expect($warning->sourceCheckId)->toBe('check.id');
});

test('AnalysisSuggestion with optional confidence', function () {
    $suggestion = new AnalysisSuggestion('Fix it', 'How to fix', 'check.id', 0.8);

    expect($suggestion->message)->toBe('Fix it');
    expect($suggestion->details)->toBe('How to fix');
    expect($suggestion->sourceCheckId)->toBe('check.id');
    expect($suggestion->confidence)->toBe(0.8);
});

test('AnalysisSuggestion without confidence', function () {
    $suggestion = new AnalysisSuggestion('Fix it', 'How to fix', 'check.id');

    expect($suggestion->confidence)->toBeNull();
});

test('DTOs are readonly classes with no setters', function () {
    $ref = new CheckReference('check.a', 'Check');
    expect($ref->id)->toBe('check.a');

    // Readonly classes cannot have properties reassigned after construction
    $reflection = new \ReflectionClass($ref);
    $props = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
    foreach ($props as $prop) {
        expect($prop->isReadOnly())->toBeTrue();
    }
});

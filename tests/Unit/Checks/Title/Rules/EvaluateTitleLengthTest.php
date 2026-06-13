<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Rules\EvaluateTitleLength;
use MegSEO\Checks\Title\Support\TitleLengthPolicy;
use MegSEO\Checks\Title\DTO\TitleNormalizationResult;

function createPolicy(): TitleLengthPolicy
{
    return new TitleLengthPolicy(
        minLength: 30,
        maxLength: 60,
        shortThreshold: 20,
        longThreshold: 70,
    );
}

test('EvaluateTitleLength returns warning for short title', function () {
    $rule = new EvaluateTitleLength(createPolicy());
    $normalized = new TitleNormalizationResult(rawTitle: 'Short', normalizedTitle: 'Short');
    $warning = $rule->evaluate($normalized);

    expect($warning)->not->toBeNull();
    expect($warning->message)->toBe('Title is too short');
    expect($warning->sourceCheckId)->toBe('seo.title');
});

test('EvaluateTitleLength returns warning for long title', function () {
    $rule = new EvaluateTitleLength(createPolicy());
    $longTitle = str_repeat('a', 75);
    $normalized = new TitleNormalizationResult(rawTitle: $longTitle, normalizedTitle: $longTitle);
    $warning = $rule->evaluate($normalized);

    expect($warning)->not->toBeNull();
    expect($warning->message)->toBe('Title is too long');
});

test('EvaluateTitleLength returns null for good length title', function () {
    $rule = new EvaluateTitleLength(createPolicy());
    $title = str_repeat('a', 40);
    $normalized = new TitleNormalizationResult(rawTitle: $title, normalizedTitle: $title);
    $warning = $rule->evaluate($normalized);

    expect($warning)->toBeNull();
});

test('EvaluateTitleLength returns null for null title', function () {
    $rule = new EvaluateTitleLength(createPolicy());
    $normalized = new TitleNormalizationResult(rawTitle: null, normalizedTitle: null);
    $warning = $rule->evaluate($normalized);

    expect($warning)->toBeNull();
});

test('EvaluateTitleLength returns null for empty title', function () {
    $rule = new EvaluateTitleLength(createPolicy());
    $normalized = new TitleNormalizationResult(rawTitle: '', normalizedTitle: '');
    $warning = $rule->evaluate($normalized);

    expect($warning)->toBeNull();
});

test('EvaluateTitleLength handles boundary values', function () {
    $rule = new EvaluateTitleLength(createPolicy());

    $atThreshold = str_repeat('a', 20);
    $normalized = new TitleNormalizationResult(rawTitle: $atThreshold, normalizedTitle: $atThreshold);
    expect($rule->evaluate($normalized))->toBeNull(); // 20 is not < 20

    $aboveThreshold = str_repeat('a', 70);
    $normalized2 = new TitleNormalizationResult(rawTitle: $aboveThreshold, normalizedTitle: $aboveThreshold);
    expect($rule->evaluate($normalized2))->toBeNull(); // 70 is not > 70
});

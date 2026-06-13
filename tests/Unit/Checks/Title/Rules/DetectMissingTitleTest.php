<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Rules\DetectMissingTitle;
use MegSEO\Checks\Title\DTO\TitleNormalizationResult;

test('DetectMissingTitle returns issue when normalized result is null', function () {
    $rule = new DetectMissingTitle();
    $issue = $rule->evaluate(null);

    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Title is missing');
    expect($issue->sourceCheckId)->toBe('seo.title');
});

test('DetectMissingTitle returns issue when rawTitle is null', function () {
    $rule = new DetectMissingTitle();
    $normalized = new TitleNormalizationResult(rawTitle: null, normalizedTitle: null);
    $issue = $rule->evaluate($normalized);

    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Title is missing');
});

test('DetectMissingTitle returns null when title is present', function () {
    $rule = new DetectMissingTitle();
    $normalized = new TitleNormalizationResult(rawTitle: 'Hello World', normalizedTitle: 'Hello World');
    $issue = $rule->evaluate($normalized);

    expect($issue)->toBeNull();
});

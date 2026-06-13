<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Rules\DetectEmptyTitle;
use MegSEO\Checks\Title\DTO\TitleNormalizationResult;

test('DetectEmptyTitle returns issue for empty string', function () {
    $rule = new DetectEmptyTitle();
    $normalized = new TitleNormalizationResult(rawTitle: '', normalizedTitle: '');
    $issue = $rule->evaluate($normalized);

    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Title is empty');
    expect($issue->sourceCheckId)->toBe('seo.title');
});

test('DetectEmptyTitle returns issue for null normalized title', function () {
    $rule = new DetectEmptyTitle();
    $normalized = new TitleNormalizationResult(rawTitle: null, normalizedTitle: null);
    $issue = $rule->evaluate($normalized);

    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Title is empty');
});

test('DetectEmptyTitle returns null for valid title', function () {
    $rule = new DetectEmptyTitle();
    $normalized = new TitleNormalizationResult(rawTitle: 'Hello', normalizedTitle: 'Hello');
    $issue = $rule->evaluate($normalized);

    expect($issue)->toBeNull();
});

test('DetectEmptyTitle returns issue for whitespace-only that normalizes to empty', function () {
    $rule = new DetectEmptyTitle();
    $normalized = new TitleNormalizationResult(rawTitle: '     ', normalizedTitle: '');
    $issue = $rule->evaluate($normalized);

    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Title is empty');
});

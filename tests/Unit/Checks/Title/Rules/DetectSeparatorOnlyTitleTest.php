<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Rules\DetectSeparatorOnlyTitle;
use MegSEO\Checks\Title\DTO\TitleNormalizationResult;

test('DetectSeparatorOnlyTitle returns issue for punctuation-only title', function () {
    $rule = new DetectSeparatorOnlyTitle();
    $normalized = new TitleNormalizationResult(rawTitle: '!!!...???', normalizedTitle: '!!!...???');
    $issue = $rule->evaluate($normalized);

    expect($issue)->not->toBeNull();
    expect($issue->message)->toContain('punctuation');
    expect($issue->sourceCheckId)->toBe('seo.title');
});

test('DetectSeparatorOnlyTitle returns issue for separator-only title', function () {
    $rule = new DetectSeparatorOnlyTitle();
    $normalized = new TitleNormalizationResult(rawTitle: '---===___', normalizedTitle: '---===___');
    $issue = $rule->evaluate($normalized);

    expect($issue)->not->toBeNull();
    expect($issue->message)->toContain('punctuation, separators');
});

test('DetectSeparatorOnlyTitle returns null for valid title', function () {
    $rule = new DetectSeparatorOnlyTitle();
    $normalized = new TitleNormalizationResult(rawTitle: 'Hello World', normalizedTitle: 'Hello World');
    $issue = $rule->evaluate($normalized);

    expect($issue)->toBeNull();
});

test('DetectSeparatorOnlyTitle returns null for null title', function () {
    $rule = new DetectSeparatorOnlyTitle();
    $normalized = new TitleNormalizationResult(rawTitle: null, normalizedTitle: null);
    $issue = $rule->evaluate($normalized);

    expect($issue)->toBeNull();
});

test('DetectSeparatorOnlyTitle returns null for empty title', function () {
    $rule = new DetectSeparatorOnlyTitle();
    $normalized = new TitleNormalizationResult(rawTitle: '', normalizedTitle: '');
    $issue = $rule->evaluate($normalized);

    expect($issue)->toBeNull();
});

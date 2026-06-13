<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Rules\EvaluateFocusKeywordPresence;
use MegSEO\Checks\Title\DTO\TitleNormalizationResult;

test('EvaluateFocusKeywordPresence returns null when no keyword supplied', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $normalized = new TitleNormalizationResult(
        rawTitle: 'Hello World',
        normalizedTitle: 'Hello World',
        normalizedFocusKeyword: null,
    );

    $suggestion = $rule->evaluate($normalized);

    expect($suggestion)->toBeNull();
});

test('EvaluateFocusKeywordPresence returns null when keyword is present in title', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $normalized = new TitleNormalizationResult(
        rawTitle: 'Welcome to MegSEO Platform',
        normalizedTitle: 'Welcome to MegSEO Platform',
        normalizedFocusKeyword: 'MegSEO',
    );

    $suggestion = $rule->evaluate($normalized);

    expect($suggestion)->toBeNull();
});

test('EvaluateFocusKeywordPresence returns suggestion when keyword is absent from title', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $normalized = new TitleNormalizationResult(
        rawTitle: 'Welcome to Our Platform',
        normalizedTitle: 'Welcome to Our Platform',
        normalizedFocusKeyword: 'MegSEO',
    );

    $suggestion = $rule->evaluate($normalized);

    expect($suggestion)->not->toBeNull();
    expect($suggestion->message)->toContain('Focus keyword');
    expect($suggestion->sourceCheckId)->toBe('seo.title');
    expect($suggestion->confidence)->toBeGreaterThan(0);
});

test('EvaluateFocusKeywordPresence handles Arabic keyword', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $normalized = new TitleNormalizationResult(
        rawTitle: 'مرحباً بكم في منصة MegSEO',
        normalizedTitle: 'مرحباً بكم في منصة MegSEO',
        normalizedFocusKeyword: 'مرحباً',
    );

    $suggestion = $rule->evaluate($normalized);

    expect($suggestion)->toBeNull();
});

test('EvaluateFocusKeywordPresence handles Arabic keyword absent', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $normalized = new TitleNormalizationResult(
        rawTitle: 'منصة تحسين المحتوى',
        normalizedTitle: 'منصة تحسين المحتوى',
        normalizedFocusKeyword: 'MegSEO',
    );

    $suggestion = $rule->evaluate($normalized);

    expect($suggestion)->not->toBeNull();
    expect($suggestion->message)->toContain('Focus keyword');
});

test('EvaluateFocusKeywordPresence handles case-insensitive match', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $normalized = new TitleNormalizationResult(
        rawTitle: 'Welcome to MEGSEO Platform',
        normalizedTitle: 'Welcome to MEGSEO Platform',
        normalizedFocusKeyword: 'megseo',
    );

    $suggestion = $rule->evaluate($normalized);

    expect($suggestion)->toBeNull();
});

test('EvaluateFocusKeywordPresence with null title and keyword', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $normalized = new TitleNormalizationResult(
        rawTitle: null,
        normalizedTitle: null,
        normalizedFocusKeyword: null,
    );

    $suggestion = $rule->evaluate($normalized);

    expect($suggestion)->toBeNull();
});

test('EvaluateFocusKeywordPresence with empty title and keyword present', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $normalized = new TitleNormalizationResult(
        rawTitle: '',
        normalizedTitle: '',
        normalizedFocusKeyword: 'something',
    );

    $suggestion = $rule->evaluate($normalized);

    expect($suggestion)->not->toBeNull();
});

<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Rules\EvaluateFocusKeywordPresence;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;

test('Returns null when no keyword supplied', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $n = new MetaDescriptionNormalizationResult('Hello World Desc', 'Hello World Desc', null);
    expect($rule->evaluate($n))->toBeNull();
});

test('Returns null when keyword is present', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $n = new MetaDescriptionNormalizationResult('Welcome to MegSEO Platform Desc', 'Welcome to MegSEO Platform Desc', 'MegSEO');
    expect($rule->evaluate($n))->toBeNull();
});

test('Returns suggestion when keyword absent', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $n = new MetaDescriptionNormalizationResult('Welcome to Our Platform', 'Welcome to Our Platform', 'MegSEO');
    $s = $rule->evaluate($n);
    expect($s)->not->toBeNull();
    expect($s->message)->toContain('Focus keyword');
    expect($s->sourceCheckId)->toBe('seo.meta_description');
});

test('Handles Arabic keyword present', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $n = new MetaDescriptionNormalizationResult('مرحباً بكم في منصة MegSEO الوصف', 'مرحباً بكم في منصة MegSEO الوصف', 'MegSEO');
    expect($rule->evaluate($n))->toBeNull();
});

test('Handles case-insensitive match', function () {
    $rule = new EvaluateFocusKeywordPresence();
    $n = new MetaDescriptionNormalizationResult('Welcome to MEGSEO Platform Description', 'Welcome to MEGSEO Platform Description', 'megseo');
    expect($rule->evaluate($n))->toBeNull();
});

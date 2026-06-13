<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Rules\DetectSeparatorOnlyMetaDescription;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;

test('Returns issue for punctuation-only', function () {
    $rule = new DetectSeparatorOnlyMetaDescription();
    $n = new MetaDescriptionNormalizationResult('!!!...???', '!!!...???');
    $issue = $rule->evaluate($n);
    expect($issue)->not->toBeNull();
    expect($issue->message)->toContain('punctuation');
});

test('Returns null for valid description', function () {
    $rule = new DetectSeparatorOnlyMetaDescription();
    $n = new MetaDescriptionNormalizationResult('Hello', 'Hello');
    expect($rule->evaluate($n))->toBeNull();
});

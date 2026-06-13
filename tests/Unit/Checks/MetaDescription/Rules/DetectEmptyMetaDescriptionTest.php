<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Rules\DetectEmptyMetaDescription;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;

test('Returns issue for empty string', function () {
    $rule = new DetectEmptyMetaDescription();
    $n = new MetaDescriptionNormalizationResult('', '');
    $issue = $rule->evaluate($n);
    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Meta description is empty');
});

test('Returns null for valid description', function () {
    $rule = new DetectEmptyMetaDescription();
    $n = new MetaDescriptionNormalizationResult('Hello', 'Hello');
    expect($rule->evaluate($n))->toBeNull();
});

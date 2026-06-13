<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Rules\DetectMissingMetaDescription;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;

test('Returns issue when normalized result is null', function () {
    $rule = new DetectMissingMetaDescription();
    $issue = $rule->evaluate(null);
    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Meta description is missing');
});

test('Returns null when description is present', function () {
    $rule = new DetectMissingMetaDescription();
    $normalized = new MetaDescriptionNormalizationResult('Hello', 'Hello');
    expect($rule->evaluate($normalized))->toBeNull();
});

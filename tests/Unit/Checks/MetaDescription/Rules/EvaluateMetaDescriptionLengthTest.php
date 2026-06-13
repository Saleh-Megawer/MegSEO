<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Rules\EvaluateMetaDescriptionLength;
use MegSEO\Checks\MetaDescription\Support\MetaDescriptionLengthPolicy;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;

test('Returns warning for short description', function () {
    $rule = new EvaluateMetaDescriptionLength(new MetaDescriptionLengthPolicy(120, 160, 80, 170));
    $n = new MetaDescriptionNormalizationResult('Short', 'Short');
    $warning = $rule->evaluate($n);
    expect($warning)->not->toBeNull();
    expect($warning->message)->toBe('Meta description is too short');
});

test('Returns warning for long description', function () {
    $rule = new EvaluateMetaDescriptionLength(new MetaDescriptionLengthPolicy(120, 160, 80, 170));
    $long = str_repeat('a', 200);
    $n = new MetaDescriptionNormalizationResult($long, $long);
    $warning = $rule->evaluate($n);
    expect($warning)->not->toBeNull();
    expect($warning->message)->toBe('Meta description is too long');
});

test('Returns null for good length', function () {
    $rule = new EvaluateMetaDescriptionLength(new MetaDescriptionLengthPolicy(120, 160, 80, 170));
    $good = str_repeat('a', 140);
    $n = new MetaDescriptionNormalizationResult($good, $good);
    expect($rule->evaluate($n))->toBeNull();
});

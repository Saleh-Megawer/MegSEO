<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Contracts\MetaDescriptionNormalizer;
use MegSEO\Checks\MetaDescription\Contracts\SupportsFocusKeyword;
use MegSEO\Checks\MetaDescription\Contracts\SupportsDuplicateDescriptions;

test('MetaDescriptionNormalizer contract defines expected methods', function () {
    $ref = new ReflectionClass(MetaDescriptionNormalizer::class);
    $methods = array_map(fn ($m) => $m->getName(), $ref->getMethods());
    expect($methods)->toContain('normalize');
});

test('SupportsFocusKeyword contract defines expected methods', function () {
    $ref = new ReflectionClass(SupportsFocusKeyword::class);
    $methods = array_map(fn ($m) => $m->getName(), $ref->getMethods());
    expect($methods)->toContain('keywordSupplied');
    expect($methods)->toContain('getNormalizedKeyword');
});

test('SupportsDuplicateDescriptions contract defines expected methods', function () {
    $ref = new ReflectionClass(SupportsDuplicateDescriptions::class);
    $methods = array_map(fn ($m) => $m->getName(), $ref->getMethods());
    expect($methods)->toContain('duplicateDataAvailable');
    expect($methods)->toContain('getDuplicateMatches');
});

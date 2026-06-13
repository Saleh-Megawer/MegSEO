<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Contracts\TitleNormalizer;
use MegSEO\Checks\Title\Contracts\SupportsFocusKeyword;
use MegSEO\Checks\Title\Contracts\SupportsDuplicateTitles;
use MegSEO\Checks\Title\DTO\TitleDuplicateMatch;

test('TitleNormalizer contract defines expected methods', function () {
    $ref = new ReflectionClass(TitleNormalizer::class);
    $methods = array_map(fn ($m) => $m->getName(), $ref->getMethods());

    expect($methods)->toContain('normalize');
});

test('SupportsFocusKeyword contract defines expected methods', function () {
    $ref = new ReflectionClass(SupportsFocusKeyword::class);
    $methods = array_map(fn ($m) => $m->getName(), $ref->getMethods());

    expect($methods)->toContain('keywordSupplied');
    expect($methods)->toContain('getNormalizedKeyword');
});

test('SupportsDuplicateTitles contract defines expected methods', function () {
    $ref = new ReflectionClass(SupportsDuplicateTitles::class);
    $methods = array_map(fn ($m) => $m->getName(), $ref->getMethods());

    expect($methods)->toContain('duplicateDataAvailable');
    expect($methods)->toContain('getDuplicateMatches');
});

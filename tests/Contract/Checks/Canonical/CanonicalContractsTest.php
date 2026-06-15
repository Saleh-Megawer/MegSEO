<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Contracts\CanonicalUrlNormalizer;
use MegSEO\Checks\Canonical\Contracts\SupportsPageUrl;

test('CanonicalUrlNormalizer contract defines normalize method', function () {
    $r = new ReflectionClass(CanonicalUrlNormalizer::class);
    $m = array_map(fn ($x) => $x->getName(), $r->getMethods());
    expect($m)->toContain('normalize');
});

test('SupportsPageUrl contract defines expected methods', function () {
    $r = new ReflectionClass(SupportsPageUrl::class);
    $m = array_map(fn ($x) => $x->getName(), $r->getMethods());
    expect($m)->toContain('pageUrlSupplied');
    expect($m)->toContain('getNormalizedPageUrl');
});

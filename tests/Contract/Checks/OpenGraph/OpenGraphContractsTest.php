<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\Contracts\OpenGraphDataProvider;

test('OpenGraphDataProvider contract defines expected methods', function () {
    $r = new ReflectionClass(OpenGraphDataProvider::class);
    $m = array_map(fn ($x) => $x->getName(), $r->getMethods());
    expect($m)->toContain('hasProperty');
    expect($m)->toContain('getProperty');
    expect($m)->toContain('all');
});

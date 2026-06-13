<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Support\MetaDescriptionLengthPolicy;

test('Policy detects short descriptions', function () {
    $p = new MetaDescriptionLengthPolicy(120, 160, 80, 170);
    expect($p->isShort(50))->toBeTrue();
    expect($p->isShort(80))->toBeFalse();
});

test('Policy detects long descriptions', function () {
    $p = new MetaDescriptionLengthPolicy(120, 160, 80, 170);
    expect($p->isLong(180))->toBeTrue();
    expect($p->isLong(170))->toBeFalse();
});

test('Policy returns recommended range', function () {
    $p = new MetaDescriptionLengthPolicy(120, 160, 80, 170);
    expect($p->getRecommendedMin())->toBe(120);
    expect($p->getRecommendedMax())->toBe(160);
});

test('Policy is framework-agnostic', function () {
    $p = new MetaDescriptionLengthPolicy(120, 160, 80, 170);
    expect($p)->toBeInstanceOf(MetaDescriptionLengthPolicy::class);
});

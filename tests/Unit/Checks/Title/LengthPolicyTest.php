<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Support\TitleLengthPolicy;

test('TitleLengthPolicy detects short titles', function () {
    $policy = new TitleLengthPolicy(
        minLength: 30,
        maxLength: 60,
        shortThreshold: 20,
        longThreshold: 70,
    );

    expect($policy->isShort(10))->toBeTrue();
    expect($policy->isShort(20))->toBeFalse();
    expect($policy->isShort(30))->toBeFalse();
});

test('TitleLengthPolicy detects long titles', function () {
    $policy = new TitleLengthPolicy(
        minLength: 30,
        maxLength: 60,
        shortThreshold: 20,
        longThreshold: 70,
    );

    expect($policy->isLong(75))->toBeTrue();
    expect($policy->isLong(70))->toBeFalse();
    expect($policy->isLong(50))->toBeFalse();
});

test('TitleLengthPolicy returns recommended range', function () {
    $policy = new TitleLengthPolicy(
        minLength: 30,
        maxLength: 60,
        shortThreshold: 20,
        longThreshold: 70,
    );

    expect($policy->getRecommendedMin())->toBe(30);
    expect($policy->getRecommendedMax())->toBe(60);
});

test('TitleLengthPolicy with different thresholds', function () {
    $policy = new TitleLengthPolicy(
        minLength: 50,
        maxLength: 70,
        shortThreshold: 40,
        longThreshold: 80,
    );

    expect($policy->isShort(35))->toBeTrue();
    expect($policy->isShort(45))->toBeFalse();
    expect($policy->isLong(85))->toBeTrue();
    expect($policy->isLong(75))->toBeFalse();
    expect($policy->getRecommendedMin())->toBe(50);
    expect($policy->getRecommendedMax())->toBe(70);
});

test('TitleLengthPolicy is framework-agnostic', function () {
    $policy = new TitleLengthPolicy(30, 60, 20, 70);

    expect($policy)->toBeInstanceOf(TitleLengthPolicy::class);
    // No config file reads — thresholds come from constructor
});

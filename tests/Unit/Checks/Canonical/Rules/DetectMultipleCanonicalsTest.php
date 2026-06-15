<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Rules\DetectMultipleCanonicals;
use MegSEO\Checks\Canonical\DTO\CanonicalCheckInput;

test('Returns issue when multiple canonicals', function () {
    $r = new DetectMultipleCanonicals();
    $i = new CanonicalCheckInput(canonical: 'https://ex.com/a', canonicalUrls: ['https://ex.com/a', 'https://ex.com/b']);
    $issue = $r->evaluate($i);
    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Multiple canonical tags detected');
});

test('Returns null for single canonical', function () {
    $r = new DetectMultipleCanonicals();
    $i = new CanonicalCheckInput(canonical: 'https://ex.com/a', canonicalUrls: ['https://ex.com/a']);
    expect($r->evaluate($i))->toBeNull();
});

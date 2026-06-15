<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Rules\DetectEmptyCanonical;
use MegSEO\Checks\Canonical\DTO\CanonicalUrlNormalizationResult;

test('Returns issue for empty canonical', function () {
    $r = new DetectEmptyCanonical();
    $n = new CanonicalUrlNormalizationResult('', '');
    $issue = $r->evaluate($n);
    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Canonical tag is empty');
});

test('Returns null for valid canonical', function () {
    $r = new DetectEmptyCanonical();
    $n = new CanonicalUrlNormalizationResult('https://ex.com', 'https://ex.com');
    expect($r->evaluate($n))->toBeNull();
});

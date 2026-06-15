<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Rules\DetectMissingCanonical;

test('Returns issue when canonical is missing', function () {
    $r = new DetectMissingCanonical();
    $issue = $r->evaluate(null);
    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Canonical tag is missing');
});

test('Returns null when canonical is present', function () {
    $r = new DetectMissingCanonical();
    $normalized = new MegSEO\Checks\Canonical\DTO\CanonicalUrlNormalizationResult('https://ex.com', 'https://ex.com');
    expect($r->evaluate($normalized))->toBeNull();
});

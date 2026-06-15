<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Rules\DetectInvalidCanonicalUrl;
use MegSEO\Checks\Canonical\DTO\CanonicalUrlNormalizationResult;

test('Returns issue for invalid scheme', function () {
    $r = new DetectInvalidCanonicalUrl();
    $n = new CanonicalUrlNormalizationResult('ftp://example.com', 'ftp://example.com');
    $issue = $r->evaluate($n);
    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('Canonical URL is invalid');
});

test('Returns null for valid URL', function () {
    $r = new DetectInvalidCanonicalUrl();
    $n = new CanonicalUrlNormalizationResult('https://example.com', 'https://example.com');
    expect($r->evaluate($n))->toBeNull();
});

test('Returns null for relative URL', function () {
    $r = new DetectInvalidCanonicalUrl();
    $n = new CanonicalUrlNormalizationResult('/relative-path', '/relative-path');
    expect($r->evaluate($n))->toBeNull();
});

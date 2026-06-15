<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Normalization\DeterministicCanonicalUrlNormalizer;

test('Normalizes trailing slash', function () {
    $n = new DeterministicCanonicalUrlNormalizer();
    $r = $n->normalize('https://example.com/page/');
    expect($r->normalizedCanonical)->toBe('https://example.com/page');
});

test('Lowercases scheme and host', function () {
    $n = new DeterministicCanonicalUrlNormalizer();
    $r = $n->normalize('HTTPS://EXAMPLE.COM/page');
    expect($r->normalizedCanonical)->toBe('https://example.com/page');
});

test('Strips default ports', function () {
    $n = new DeterministicCanonicalUrlNormalizer();
    $r = $n->normalize('https://example.com:443/page');
    expect($r->normalizedCanonical)->toBe('https://example.com/page');
});

test('Sorts query parameters', function () {
    $n = new DeterministicCanonicalUrlNormalizer();
    $r = $n->normalize('https://example.com/page?b=2&a=1');
    expect($r->normalizedCanonical)->toBe('https://example.com/page?a=1&b=2');
});

test('Handles null input', function () {
    $n = new DeterministicCanonicalUrlNormalizer();
    $r = $n->normalize(null);
    expect($r->normalizedCanonical)->toBeNull();
});

test('Deterministic output', function () {
    $n = new DeterministicCanonicalUrlNormalizer();
    $r1 = $n->normalize('HTTPS://Example.COM:443/page/?a=1');
    $r2 = $n->normalize('HTTPS://Example.COM:443/page/?a=1');
    expect($r1->normalizedCanonical)->toBe($r2->normalizedCanonical);
});

test('Normalizes page URL alongside canonical', function () {
    $n = new DeterministicCanonicalUrlNormalizer();
    $r = $n->normalize('https://example.com/canonical', 'HTTPS://example.com:443/page/');
    expect($r->normalizedCanonical)->toBe('https://example.com/canonical');
    expect($r->normalizedPageUrl)->toBe('https://example.com/page');
});

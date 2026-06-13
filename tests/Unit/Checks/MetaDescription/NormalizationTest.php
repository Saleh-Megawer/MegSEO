<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Normalization\DeterministicMetaDescriptionNormalizer;

test('Normalizer trims whitespace', function () {
    $n = new DeterministicMetaDescriptionNormalizer();
    $r = $n->normalize('  Hello Description  ');
    expect($r->normalizedDescription)->toBe('Hello Description');
});

test('Normalizer collapses internal whitespace', function () {
    $n = new DeterministicMetaDescriptionNormalizer();
    $r = $n->normalize("Hello    Description\t\nText");
    expect($r->normalizedDescription)->toBe('Hello Description Text');
});

test('Normalizer produces identical output for identical input', function () {
    $n = new DeterministicMetaDescriptionNormalizer();
    $r1 = $n->normalize("  Hello   World\t\n  ");
    $r2 = $n->normalize("  Hello   World\t\n  ");
    expect($r1->normalizedDescription)->toBe($r2->normalizedDescription);
});

test('Normalizer handles null input', function () {
    $n = new DeterministicMetaDescriptionNormalizer();
    $r = $n->normalize(null);
    expect($r->normalizedDescription)->toBeNull();
    expect($r->rawDescription)->toBeNull();
});

test('Normalizer handles Arabic text', function () {
    $n = new DeterministicMetaDescriptionNormalizer();
    $r = $n->normalize('هذا وصف تجريبي للصفحة');
    expect($r->normalizedDescription)->toBe('هذا وصف تجريبي للصفحة');
});

test('Normalizer normalizes focus keyword', function () {
    $n = new DeterministicMetaDescriptionNormalizer();
    $r = $n->normalize('Hello World Description', '  hello  ');
    expect($r->normalizedFocusKeyword)->toBe('hello');
});

test('Normalizer handles Unicode NFKC', function () {
    $n = new DeterministicMetaDescriptionNormalizer();
    $r = $n->normalize('Hello ﬁ World');
    expect($r->normalizedDescription)->toContain('fi');
});

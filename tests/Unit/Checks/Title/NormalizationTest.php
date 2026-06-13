<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Normalization\DeterministicTitleNormalizer;

test('DeterministicTitleNormalizer trims whitespace', function () {
    $normalizer = new DeterministicTitleNormalizer();
    $result = $normalizer->normalize('  Hello World  ');

    expect($result->normalizedTitle)->toBe('Hello World');
});

test('DeterministicTitleNormalizer collapses internal whitespace', function () {
    $normalizer = new DeterministicTitleNormalizer();
    $result = $normalizer->normalize("Hello    World\t\nExtra");

    expect($result->normalizedTitle)->toBe('Hello World Extra');
});

test('DeterministicTitleNormalizer produces identical output for identical input', function () {
    $normalizer = new DeterministicTitleNormalizer();

    $result1 = $normalizer->normalize("  Hello   World\t\n  ");
    $result2 = $normalizer->normalize("  Hello   World\t\n  ");

    expect($result1->normalizedTitle)->toBe($result2->normalizedTitle);
});

test('DeterministicTitleNormalizer handles null input', function () {
    $normalizer = new DeterministicTitleNormalizer();
    $result = $normalizer->normalize(null);

    expect($result->normalizedTitle)->toBeNull();
    expect($result->rawTitle)->toBeNull();
    expect($result->flags['action'] ?? null)->toBe('skip-null');
});

test('DeterministicTitleNormalizer handles empty string', function () {
    $normalizer = new DeterministicTitleNormalizer();
    $result = $normalizer->normalize('');

    expect($result->normalizedTitle)->toBe('');
    expect($result->rawTitle)->toBe('');
});

test('DeterministicTitleNormalizer handles Arabic text', function () {
    $normalizer = new DeterministicTitleNormalizer();
    $result = $normalizer->normalize('عنوان الصفحة');

    expect($result->normalizedTitle)->toBe('عنوان الصفحة');
});

test('DeterministicTitleNormalizer handles mixed Arabic-English', function () {
    $normalizer = new DeterministicTitleNormalizer();
    $result = $normalizer->normalize("  مرحباً  World  ");

    expect($result->normalizedTitle)->toBe('مرحباً World');
});

test('DeterministicTitleNormalizer normalizes focus keyword', function () {
    $normalizer = new DeterministicTitleNormalizer();
    $result = $normalizer->normalize('Hello World', '  hello  ');

    expect($result->normalizedTitle)->toBe('Hello World');
    expect($result->normalizedFocusKeyword)->toBe('hello');
});

test('DeterministicTitleNormalizer handles null focus keyword', function () {
    $normalizer = new DeterministicTitleNormalizer();
    $result = $normalizer->normalize('Hello World', null);

    expect($result->normalizedFocusKeyword)->toBeNull();
});

test('DeterministicTitleNormalizer handles empty focus keyword', function () {
    $normalizer = new DeterministicTitleNormalizer();
    $result = $normalizer->normalize('Hello World', '');

    expect($result->normalizedFocusKeyword)->toBeNull();
});

test('DeterministicTitleNormalizer handles Unicode NFKC normalization', function () {
    $normalizer = new DeterministicTitleNormalizer();
    $result = $normalizer->normalize('Hello ﬁ World');

    expect($result->normalizedTitle)->toContain('fi');
    expect($result->flags['nfkc_applied'] ?? null)->toBeTrue();
});

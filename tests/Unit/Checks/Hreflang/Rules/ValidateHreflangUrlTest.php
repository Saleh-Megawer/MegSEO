<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\Rules\ValidateHreflangUrl;

test('Valid URL passes', function () {
    expect((new ValidateHreflangUrl())->evaluate('https://example.com/en', false))->toBeNull();
});

test('Relative URL returns warning', function () {
    $w = (new ValidateHreflangUrl())->evaluate('/en', false);
    expect($w)->not->toBeNull();
    expect($w->message)->toBe('hreflang href is relative');
});

test('Invalid URL returns warning', function () {
    $w = (new ValidateHreflangUrl())->evaluate('ftp://bad', false);
    expect($w)->not->toBeNull();
    expect($w->message)->toBe('hreflang href is invalid');
});

test('Arabic URL accepted', function () {
    expect((new ValidateHreflangUrl())->evaluate('https://example.com/عربي', false))->toBeNull();
});

test('Empty suppressed', function () {
    expect((new ValidateHreflangUrl())->evaluate('', true))->toBeNull();
});

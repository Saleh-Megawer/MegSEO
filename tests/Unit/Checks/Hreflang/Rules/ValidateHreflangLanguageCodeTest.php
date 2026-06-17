<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\Rules\ValidateHreflangLanguageCode;

test('Valid codes pass', function () {
    $r = new ValidateHreflangLanguageCode();
    foreach (['en', 'en-US', 'zh-Hans', 'es-MX', 'sr-Latn-RS', 'x-default'] as $c) {
        expect($r->evaluate($c, false))->toBeNull();
    }
});

test('Invalid code returns warning', function () {
    $r = new ValidateHreflangLanguageCode();
    expect($r->evaluate('invalid!', false))->not->toBeNull();
});

test('Empty suppressed', function () {
    $r = new ValidateHreflangLanguageCode();
    expect($r->evaluate('', true))->toBeNull();
    expect($r->evaluate(null, true))->toBeNull();
});

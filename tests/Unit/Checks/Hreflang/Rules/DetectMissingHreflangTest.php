<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\Rules\DetectMissingHreflang;
use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;

test('Returns issue when entries empty', function () {
    $r = new DetectMissingHreflang();
    expect($r->evaluate(new HreflangCheckInput([])))->not->toBeNull();
});

test('Returns null when entries present', function () {
    $r = new DetectMissingHreflang();
    expect($r->evaluate(new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com']])))->toBeNull();
});

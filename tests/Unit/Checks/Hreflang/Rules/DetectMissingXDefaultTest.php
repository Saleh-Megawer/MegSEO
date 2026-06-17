<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\Rules\DetectMissingXDefault;
use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;

test('Suggests when 2+ entries and no x-default', function () {
    $r = new DetectMissingXDefault();
    $i = new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com/en'], ['hreflang'=>'fr','href'=>'https://x.com/fr']]);
    expect($r->evaluate($i))->not->toBeNull();
});

test('Returns null with x-default present', function () {
    $r = new DetectMissingXDefault();
    $i = new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com/en'], ['hreflang'=>'x-default','href'=>'https://x.com/']]);
    expect($r->evaluate($i))->toBeNull();
});

test('Returns null for single entry', function () {
    $r = new DetectMissingXDefault();
    $i = new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com/en']]);
    expect($r->evaluate($i))->toBeNull();
});

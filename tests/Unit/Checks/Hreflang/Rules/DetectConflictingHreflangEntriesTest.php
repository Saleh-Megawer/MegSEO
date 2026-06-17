<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\Rules\DetectConflictingHreflangEntries;
use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;

test('Duplicate language codes → suggestion', function () {
    $r = new DetectConflictingHreflangEntries();
    $i = new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com/en'], ['hreflang'=>'en','href'=>'https://x.com/english']]);
    $f = $r->evaluate($i);
    expect($f)->toHaveCount(1);
    expect($f[0]->message)->toContain('Duplicate');
});

test('Same href for different languages → warning', function () {
    $r = new DetectConflictingHreflangEntries();
    $i = new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com/home'], ['hreflang'=>'fr','href'=>'https://x.com/home']]);
    $f = $r->evaluate($i);
    expect($f)->toHaveCount(1);
    expect($f[0]->message)->toContain('Same href');
});

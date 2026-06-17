<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\Rules\DetectEmptyHreflangValues;
use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;

test('Flags empty hreflang', function () {
    $r = new DetectEmptyHreflangValues();
    $i = new HreflangCheckInput([['hreflang'=>'','href'=>'https://x.com']]);
    $issues = $r->evaluate($i);
    expect($issues)->toHaveCount(1);
    expect($issues[0]->message)->toContain('empty');
});

test('Flags whitespace-only hreflang', function () {
    $r = new DetectEmptyHreflangValues();
    $i = new HreflangCheckInput([['hreflang'=>'   ','href'=>'https://x.com']]);
    expect($r->evaluate($i))->toHaveCount(1);
});

test('All valid returns none', function () {
    $r = new DetectEmptyHreflangValues();
    $i = new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com']]);
    expect($r->evaluate($i))->toBe([]);
});

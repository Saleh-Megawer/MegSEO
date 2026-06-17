<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;
use MegSEO\Checks\Hreflang\DTO\HreflangEntryReport;
use MegSEO\Checks\Hreflang\DTO\HreflangCheckMetadata;

test('HreflangCheckInput stores entries', function () {
    $i = new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com/en']]);
    expect($i->hasEntries())->toBeTrue();
    expect($i->entryCount())->toBe(1);
    expect($i->getHreflang(0))->toBe('en');
    expect($i->getHref(0))->toBe('https://x.com/en');
});

test('HreflangCheckInput detects empty', function () {
    $i = new HreflangCheckInput([['hreflang'=>'','href'=>'https://x.com']]);
    expect($i->isHreflangEmpty(0))->toBeTrue();
    expect($i->isHrefEmpty(0))->toBeFalse();
});

test('HreflangEntryReport stores status', function () {
    $r = new HreflangEntryReport(0, 'en', 'https://x.com', 'valid');
    expect($r->status)->toBe('valid');
});

test('HreflangCheckMetadata stores flags', function () {
    $m = new HreflangCheckMetadata('seo.hreflang', entryCount: 3, hasXDefault: true);
    expect($m->checkIdentifier)->toBe('seo.hreflang');
    expect($m->entryCount)->toBe(3);
});

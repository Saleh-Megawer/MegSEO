<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\DTO\CanonicalCheckInput;
use MegSEO\Checks\Canonical\DTO\CanonicalUrlNormalizationResult;
use MegSEO\Checks\Canonical\DTO\CanonicalUrlMatchReport;
use MegSEO\Checks\Canonical\DTO\CanonicalCheckMetadata;

test('CanonicalCheckInput stores values', function () {
    $i = new CanonicalCheckInput(canonical: 'https://example.com', pageUrl: 'https://example.com/page');
    expect($i->canonical)->toBe('https://example.com');
    expect($i->hasCanonical())->toBeTrue();
    expect($i->hasPageUrl())->toBeTrue();
    expect($i->hasMultipleCanonicals())->toBeFalse();
});

test('CanonicalCheckInput detects multiple canonicals', function () {
    $i = new CanonicalCheckInput(canonical: 'https://ex.com/a', canonicalUrls: ['https://ex.com/a', 'https://ex.com/b']);
    expect($i->hasMultipleCanonicals())->toBeTrue();
});

test('CanonicalUrlNormalizationResult stores normalized URLs', function () {
    $r = new CanonicalUrlNormalizationResult('https://EXAMPLE.COM/', 'https://example.com', 'https://EXAMPLE.COM/page');
    expect($r->normalizedCanonical)->toBe('https://example.com');
    expect($r->rawPageUrl)->toBe('https://EXAMPLE.COM/page');
});

test('CanonicalUrlMatchReport stores match flags', function () {
    $m = new CanonicalUrlMatchReport(isSelfReferencing: true, isRelative: false, isCrossDomain: false);
    expect($m->isSelfReferencing)->toBeTrue();
    expect($m->isCrossDomain)->toBeFalse();
});

test('CanonicalCheckMetadata stores canonical metadata', function () {
    $m = new CanonicalCheckMetadata(checkIdentifier: 'seo.canonical', multipleCanonicalsDetected: true, ruleIdentifiers: ['r1']);
    expect($m->checkIdentifier)->toBe('seo.canonical');
    expect($m->multipleCanonicalsDetected)->toBeTrue();
    expect($m->ruleIdentifiers)->toBe(['r1']);
});

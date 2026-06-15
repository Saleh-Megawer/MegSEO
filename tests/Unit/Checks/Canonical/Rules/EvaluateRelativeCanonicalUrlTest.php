<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Rules\EvaluateRelativeCanonicalUrl;
use MegSEO\Checks\Canonical\DTO\CanonicalUrlMatchReport;

test('Returns warning for relative URL', function () {
    $r = new EvaluateRelativeCanonicalUrl();
    $report = new CanonicalUrlMatchReport(isRelative: true);
    $w = $r->evaluate($report);
    expect($w)->not->toBeNull();
    expect($w->message)->toBe('Canonical URL is relative');
    expect($w->sourceCheckId)->toBe('seo.canonical');
});

test('Returns null for absolute URL', function () {
    $r = new EvaluateRelativeCanonicalUrl();
    $report = new CanonicalUrlMatchReport(isRelative: false);
    expect($r->evaluate($report))->toBeNull();
});

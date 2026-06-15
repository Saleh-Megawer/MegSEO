<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Rules\EvaluateCrossDomainCanonical;
use MegSEO\Checks\Canonical\DTO\CanonicalUrlMatchReport;

test('Returns suggestion for cross-domain', function () {
    $r = new EvaluateCrossDomainCanonical();
    $report = new CanonicalUrlMatchReport(isCrossDomain: true);
    $s = $r->evaluate($report);
    expect($s)->not->toBeNull();
    expect($s->message)->toContain('different domain');
    expect($s->sourceCheckId)->toBe('seo.canonical');
});

test('Returns null for same domain', function () {
    $r = new EvaluateCrossDomainCanonical();
    $report = new CanonicalUrlMatchReport(isCrossDomain: false);
    expect($r->evaluate($report))->toBeNull();
});

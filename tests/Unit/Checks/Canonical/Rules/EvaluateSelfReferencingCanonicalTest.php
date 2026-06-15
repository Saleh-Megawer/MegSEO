<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Rules\EvaluateSelfReferencingCanonical;
use MegSEO\Checks\Canonical\DTO\CanonicalUrlMatchReport;

test('Returns null when self-referencing', function () {
    $r = new EvaluateSelfReferencingCanonical();
    $report = new CanonicalUrlMatchReport(isSelfReferencing: true);
    expect($r->evaluate($report))->toBeNull();
});

test('Returns null for relative URL', function () {
    $r = new EvaluateSelfReferencingCanonical();
    $report = new CanonicalUrlMatchReport(isSelfReferencing: false, isRelative: true);
    expect($r->evaluate($report))->toBeNull();
});

test('Returns suggestion when not self-referencing', function () {
    $r = new EvaluateSelfReferencingCanonical();
    $report = new CanonicalUrlMatchReport(isSelfReferencing: false, isRelative: false, hasPageUrl: true);
    $s = $r->evaluate($report);
    expect($s)->not->toBeNull();
    expect($s->message)->toContain('self-reference');
});

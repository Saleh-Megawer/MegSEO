<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\CanonicalCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('IDN domain canonical works', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(subject: 'https://münchen.example.com/page'));
    expect($r->issues())->toBe([]);
});

test('URL with Unicode path works', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(subject: 'https://example.com/مرحبا'));
    expect($r->issues())->toBe([]);
});

test('Normalized trailing slash deterministic', function () {
    $c = new CanonicalCheck();
    $r1 = $c->analyze(new AnalysisContext(subject: 'https://EXAMPLE.COM:443/page/'));
    $r2 = $c->analyze(new AnalysisContext(subject: 'https://example.com/page'));
    expect($r1->metadata['normalizationApplied'])->toBeTrue();
    expect($r2->metadata['normalizationApplied'])->toBeFalse();
});

test('Cross-domain with normalization deterministic', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $c = new AnalysisContext(subject: 'https://other.com:443/path/', attributes: ['page_url' => 'https://example.com/page']);
    $r1 = $e->analyze($c);
    $r2 = $e->analyze($c);
    expect($r1->toArray())->toBe($r2->toArray());
});

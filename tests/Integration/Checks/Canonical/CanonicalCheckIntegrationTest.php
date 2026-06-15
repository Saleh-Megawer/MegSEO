<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\CanonicalCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Valid canonical passes through engine', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(subject: 'https://example.com/page'));
    expect($r->issues())->toBe([]);
});

test('Missing canonical produces issue through engine', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(subject: null));
    expect($r->issues())->toHaveCount(1);
    expect($r->issues()[0]->message)->toBe('Canonical tag is missing');
});

test('Invalid canonical produces issue through engine', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(subject: 'ftp://bad.scheme'));
    expect($r->issues())->toHaveCount(1);
    expect($r->issues()[0]->message)->toBe('Canonical URL is invalid');
});

test('Multiple canonicals detected through engine', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['canonicals' => ['https://ex.com/a', 'https://ex.com/b']]));
    expect($r->issues())->toHaveCount(1);
    expect($r->issues()[0]->message)->toBe('Multiple canonical tags detected');
});

test('Deterministic repeated runs', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $c = new AnalysisContext(subject: 'https://example.com/page');
    expect($e->analyze($c)->toArray())->toBe($e->analyze($c)->toArray());
});

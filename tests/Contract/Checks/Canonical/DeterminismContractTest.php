<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\CanonicalCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Deterministic repeated runs', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $c = new AnalysisContext(subject: 'https://example.com/page', attributes: ['page_url' => 'https://other.com/page']);
    expect($e->analyze($c)->toArray())->toBe($e->analyze($c)->toArray());
});

test('Stable identifier', function () {
    $c = new CanonicalCheck();
    expect($c->ref()->id)->toBe('seo.canonical');
    expect($c->ref()->id)->toBe($c->ref()->id);
});

test('Metadata stability', function () {
    $c = new CanonicalCheck();
    $ctx = new AnalysisContext(subject: 'https://example.com/page', attributes: ['page_url' => 'https://example.com/page']);
    expect($c->analyze($ctx)->metadata)->toBe($c->analyze($ctx)->metadata);
});

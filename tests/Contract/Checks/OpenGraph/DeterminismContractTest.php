<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\OpenGraphCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Deterministic repeated runs', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $c = new AnalysisContext(subject: ['og:title' => 'Hi', 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg']);
    expect($e->analyze($c)->toArray())->toBe($e->analyze($c)->toArray());
});

test('Stable identifier', function () {
    $c = new OpenGraphCheck();
    expect($c->ref()->id)->toBe('seo.open_graph');
    expect($c->ref()->id)->toBe($c->ref()->id);
});

test('Metadata stability', function () {
    $c = new OpenGraphCheck();
    $ctx = new AnalysisContext(subject: ['og:title' => 'Hi', 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg']);
    expect($c->analyze($ctx)->metadata)->toBe($c->analyze($ctx)->metadata);
});

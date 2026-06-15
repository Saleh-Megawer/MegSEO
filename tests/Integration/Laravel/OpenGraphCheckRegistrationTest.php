<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\OpenGraphCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Registered via facade works', function () {
    /** @var Engine $engine */
    $engine = $this->app->make(Engine::class);
    $engine->register(new OpenGraphCheck());
    $r = $engine->analyze(new AnalysisContext(subject: []));
    expect($r->issues())->toHaveCount(3);
    expect($r->issues()[0]->sourceCheckId)->toBe('seo.open_graph');
});

test('Score computed via engine', function () {
    /** @var Engine $engine */
    $engine = $this->app->make(Engine::class);
    $engine->register(new OpenGraphCheck());
    $r = $engine->analyze(new AnalysisContext(subject: ['og:title' => 'Hi', 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg']));
    expect($r->score()->value)->toBeGreaterThan(0);
    expect($r->score()->contributors)->toHaveCount(1);
});

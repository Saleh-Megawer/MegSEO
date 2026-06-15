<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\CanonicalCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Registered via facade works', function () {
    /** @var Engine $engine */
    $engine = $this->app->make(Engine::class);
    $engine->register(new CanonicalCheck());
    $result = $engine->analyze(new AnalysisContext(subject: null));
    expect($result->issues())->toHaveCount(1);
    expect($result->issues()[0]->sourceCheckId)->toBe('seo.canonical');
});

test('Score computed via engine', function () {
    /** @var Engine $engine */
    $engine = $this->app->make(Engine::class);
    $engine->register(new CanonicalCheck());
    $result = $engine->analyze(new AnalysisContext(subject: 'https://example.com/page'));
    expect($result->score()->value)->toBeGreaterThan(0);
    expect($result->score()->contributors)->toHaveCount(1);
    expect($result->score()->contributors[0]['sourceCheckId'])->toBe('seo.canonical');
});

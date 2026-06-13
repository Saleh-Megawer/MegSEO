<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('MetaDescriptionCheck registered via facade works', function () {
    /** @var Engine $engine */
    $engine = $this->app->make(Engine::class);
    $engine->register(new MetaDescriptionCheck());
    $result = $engine->analyze(new AnalysisContext(subject: null));
    expect($result->issues())->toHaveCount(1);
    expect($result->issues()[0]->sourceCheckId)->toBe('seo.meta_description');
});

test('Score computed via engine', function () {
    /** @var Engine $engine */
    $engine = $this->app->make(Engine::class);
    $engine->register(new MetaDescriptionCheck());
    $desc = str_repeat('A meta description that is long enough to pass the length threshold check for Meta. ', 3);
    $result = $engine->analyze(new AnalysisContext(subject: $desc));
    expect($result->score()->value)->toBeGreaterThan(0);
    expect($result->score()->contributors)->toHaveCount(1);
    expect($result->score()->contributors[0]['sourceCheckId'])->toBe('seo.meta_description');
});

test('Config thresholds available', function () {
    $this->app['config']->set('megseo.meta_description', ['min_length' => 120, 'max_length' => 160, 'short_threshold' => 80, 'long_threshold' => 170]);
    $c = $this->app['config']->get('megseo.meta_description');
    expect($c['min_length'])->toBe(120);
    expect($c['max_length'])->toBe(160);
});

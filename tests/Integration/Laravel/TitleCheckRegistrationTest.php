<?php

declare(strict_types=1);

use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('TitleCheck registers and participates in analysis via Laravel config', function () {
    $this->app['config']->set('megseo.checks', [
        MegSEO\Checks\Title\TitleCheck::class,
    ]);

    /** @var Engine $engine */
    $engine = $this->app->make(Engine::class);

    // Config-driven registration runs in boot()
    $this->app->call([$this->app->getProvider('MegSEO\Laravel\Providers\MegSEOServiceProvider'), 'boot']);

    $context = new AnalysisContext(subject: 'A Page Title That Is Long Enough To Pass Thresholds');
    $result = $engine->analyze($context);

    expect($result->issues())->toBe([]);
    expect($result->warnings())->toBe([]);
});

test('TitleCheck can be registered via facade', function () {
    /** @var Engine $engine */
    $engine = $this->app->make(Engine::class);

    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: null);
    $result = $engine->analyze($context);

    expect($result->issues())->toHaveCount(1);
    expect($result->issues()[0]->sourceCheckId)->toBe('seo.title');
});

test('TitleCheck score is computed via engine', function () {
    /** @var Engine $engine */
    $engine = $this->app->make(Engine::class);
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: 'A Title That Is Long Enough For SEO Analysis And Passes Checks');
    $result = $engine->analyze($context);

    expect($result->score()->value)->toBeGreaterThan(0);
    expect($result->score()->contributors)->toHaveCount(1);
    expect($result->score()->contributors[0]['sourceCheckId'])->toBe('seo.title');
    expect($result->score()->contributors[0]['value'])->toBe(100.0);
});

test('Title threshold config keys are available', function () {
    $this->app['config']->set('megseo.title', [
        'min_length' => 30,
        'max_length' => 60,
        'short_threshold' => 20,
        'long_threshold' => 70,
    ]);

    $config = $this->app['config']->get('megseo.title');

    expect($config['min_length'])->toBe(30);
    expect($config['max_length'])->toBe(60);
    expect($config['short_threshold'])->toBe(20);
    expect($config['long_threshold'])->toBe(70);
});

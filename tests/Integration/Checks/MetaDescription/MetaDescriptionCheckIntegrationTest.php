<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Valid description passes', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = str_repeat('A valid meta description with enough characters to pass all checks and thresholds. ', 2);
    $result = $engine->analyze(new AnalysisContext(subject: $desc));
    expect($result->issues())->toBe([]);
    expect($result->warnings())->toBe([]);
});

test('Missing description produces issue', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $result = $engine->analyze(new AnalysisContext(subject: null));
    expect($result->issues())->toHaveCount(1);
    expect($result->issues()[0]->message)->toBe('Meta description is missing');
});

test('Empty description produces issue', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $result = $engine->analyze(new AnalysisContext(subject: ''));
    expect($result->issues())->toHaveCount(1);
});

test('Separator-only description produces issue', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $result = $engine->analyze(new AnalysisContext(subject: '!!!...???'));
    expect($result->issues())->toHaveCount(1);
});

test('Short description produces warning', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $result = $engine->analyze(new AnalysisContext(subject: 'Too short'));
    expect($result->warnings())->toHaveCount(1);
    expect($result->warnings()[0]->message)->toBe('Meta description is too short');
});

test('Long description produces warning', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $result = $engine->analyze(new AnalysisContext(subject: str_repeat('a', 200)));
    expect($result->warnings())->toHaveCount(1);
    expect($result->warnings()[0]->message)->toBe('Meta description is too long');
});

test('Deterministic repeated runs', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = str_repeat('A valid meta description with enough characters to pass all checks and thresholds. ', 2);
    $c = new AnalysisContext(subject: $desc);
    expect($engine->analyze($c)->toArray())->toBe($engine->analyze($c)->toArray());
});

<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\OpenGraphCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Valid OG passes through engine', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:title' => 'Hi', 'og:description' => 'Desc', 'og:image' => 'https://x.com/i.jpg']));
    expect($r->issues())->toBe([]);
});

test('Missing og:title through engine', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:description' => 'Desc', 'og:image' => 'https://x.com/i.jpg']));
    expect($r->issues())->toHaveCount(1);
    expect($r->issues()[0]->message)->toBe('og:title is missing');
});

test('Empty suppresses missing through engine', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:title' => '', 'og:description' => 'Desc', 'og:image' => 'https://x.com/i.jpg']));
    expect($r->issues())->toHaveCount(1);
    expect($r->issues()[0]->message)->toBe('og:title is empty');
});

test('All missing through engine', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: []));
    expect($r->issues())->toHaveCount(3);
});

test('Deterministic repeated runs', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $c = new AnalysisContext(subject: ['og:title' => 'Hi', 'og:description' => 'Desc', 'og:image' => 'https://x.com/i.jpg']);
    expect($e->analyze($c)->toArray())->toBe($e->analyze($c)->toArray());
});

<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\OpenGraphCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Relative og:image URL produces warning', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:title' => 'Hi', 'og:image' => '/img.jpg']));
    expect($r->warnings())->toHaveCount(1);
    expect($r->warnings()[0]->message)->toBe('og:image URL is relative');
});

test('Arabic og:image URL accepted', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:title' => 'Hi', 'og:description' => 'Desc', 'og:image' => 'https://example.com/صورة.jpg']));
    expect($r->warnings())->toBe([]);
    expect($r->issues())->toBe([]);
});

test('Conflicting values produce suggestion', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:title' => ['A', 'B'], 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg']));
    expect($r->suggestions())->toHaveCount(1);
    expect($r->suggestions()[0]->message)->toContain('Conflicting');
});

test('Duplicate identical values produce no conflict', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:title' => ['Same', 'Same'], 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg']));
    expect($r->suggestions())->toBe([]);
});

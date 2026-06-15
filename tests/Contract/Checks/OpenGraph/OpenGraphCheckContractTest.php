<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\OpenGraphCheck;
use MegSEO\DTO\AnalysisContext;

test('Implements Check with stable identifier', function () {
    $c = new OpenGraphCheck();
    expect($c->ref()->id)->toBe('seo.open_graph');
    expect($c->ref()->label)->toBe('Open Graph Check');
});

test('Valid OG passes without issues', function () {
    $c = new OpenGraphCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: [
        'og:title' => 'My Title', 'og:description' => 'Desc', 'og:image' => 'https://img.jpg',
    ]));
    expect($outcome->issues)->toBe([]);
});

test('Missing og:title produces issue', function () {
    $c = new OpenGraphCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: ['og:description' => 'Desc', 'og:image' => 'https://img.jpg']));
    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('og:title is missing');
});

test('Empty og:title does NOT produce missing issue', function () {
    $c = new OpenGraphCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: ['og:title' => '', 'og:description' => 'Desc', 'og:image' => 'https://img.jpg']));
    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('og:title is empty');
    // Verify no "missing" issue for og:title
    $messages = array_map(fn ($i) => $i->message, $outcome->issues);
    expect($messages)->not->toContain('og:title is missing');
});

test('All missing produces 3 issues', function () {
    $c = new OpenGraphCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: []));
    expect($outcome->issues)->toHaveCount(3);
});

test('All empty produces 3 issues', function () {
    $c = new OpenGraphCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: ['og:title' => '', 'og:description' => '', 'og:image' => '']));
    expect($outcome->issues)->toHaveCount(3);
});

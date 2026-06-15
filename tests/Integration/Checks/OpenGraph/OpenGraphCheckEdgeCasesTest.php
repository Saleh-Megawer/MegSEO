<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\OpenGraphCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Whitespace-only og:title treated as empty not missing', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:title' => '   ', 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg']));
    $msgs = array_map(fn($i) => $i->message, $r->issues());
    expect($msgs)->toContain('og:title is empty');
    expect($msgs)->not->toContain('og:title is missing');
});

test('Mixed empty and missing — correct counts', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:title' => '', 'og:image' => 'https://x.com/i.jpg']));
    // og:title empty (1), og:description missing (1) = 2
    expect($r->issues())->toHaveCount(2);
});

test('Duplicate identical image values no conflict', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:title' => 'Hi', 'og:description' => 'D', 'og:image' => ['a.jpg', 'a.jpg']]));
    expect($r->suggestions())->toBe([]);
});

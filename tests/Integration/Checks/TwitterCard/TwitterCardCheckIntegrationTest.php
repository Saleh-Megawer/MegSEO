<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\TwitterCardCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Valid passes', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    expect($e->analyze(new AnalysisContext(subject: ['twitter:card'=>'summary','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']))->issues())->toBe([]);
});

test('Missing twitter:card through engine', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']));
    expect($r->issues())->toHaveCount(1);
    expect($r->issues()[0]->message)->toBe('twitter:card is missing');
});

test('Empty suppresses missing', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['twitter:card'=>'','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']));
    $msgs = array_map(fn($i) => $i->message, $r->issues());
    expect($msgs)->toContain('twitter:card is empty');
    expect($msgs)->not->toContain('twitter:card is missing');
});

test('Deterministic', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $c = new AnalysisContext(subject: ['twitter:card'=>'summary','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']);
    expect($e->analyze($c)->toArray())->toBe($e->analyze($c)->toArray());
});

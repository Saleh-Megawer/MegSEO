<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\HreflangCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('x-default missing with 2+ entries', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com/en'], ['hreflang'=>'fr','href'=>'https://x.com/fr']]));
    expect($r->suggestions())->toHaveCount(1);
    expect($r->suggestions()[0]->message)->toContain('x-default');
});

test('x-default present — no suggestion', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com/en'], ['hreflang'=>'x-default','href'=>'https://x.com/']]));
    expect($r->suggestions())->toBe([]);
});

test('Self-reference mismatch', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com/home']], attributes: ['page_url'=>'https://x.com/en', 'page_language'=>'en']));
    expect($r->suggestions())->toHaveCount(1);
});

test('Self-reference match passes', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com/en']], attributes: ['page_url'=>'https://x.com/en', 'page_language'=>'en']));
    expect($r->suggestions())->toBe([]);
});

test('Missing page_url skips self-reference gracefully', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com/en']]));
    expect($r->failures)->toBe([]);
});

test('Duplicate lang codes detected', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com/en'], ['hreflang'=>'en','href'=>'https://x.com/english'], ['hreflang'=>'x-default','href'=>'https://x.com/']]));
    expect($r->suggestions())->toHaveCount(1);
    expect($r->suggestions()[0]->message)->toContain('Duplicate');
});

test('Same href different lang warning', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com/home'], ['hreflang'=>'fr','href'=>'https://x.com/home'], ['hreflang'=>'x-default','href'=>'https://x.com/']]));
    expect($r->warnings())->toHaveCount(1);
    expect($r->warnings()[0]->message)->toContain('Same href');
});

<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\HreflangCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Valid through engine', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com/en']]));
    expect($r->issues())->toBe([]);
});

test('Missing through engine', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: []));
    expect($r->issues())->toHaveCount(1);
});

test('Invalid lang code through engine', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en_US!','href'=>'https://x.com']]));
    expect($r->warnings())->toHaveCount(1);
});

test('Arabic URL accepted', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'ar','href'=>'https://example.com/عربي']]));
    expect($r->warnings())->toBe([]);
});

test('Deterministic', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $c = new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com']]);
    expect($e->analyze($c)->toArray())->toBe($e->analyze($c)->toArray());
});

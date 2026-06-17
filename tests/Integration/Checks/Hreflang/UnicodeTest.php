<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\HreflangCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Arabic URLs accepted', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'ar','href'=>'https://example.com/عربي']]));
    expect($r->warnings())->toBe([]);
});

test('IDN URL accepted', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://موقعي.مصر/page']]));
    expect($r->warnings())->toBe([]);
});

<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\HreflangCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Whitespace hreflang treated as empty', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'   ','href'=>'https://x.com/en']]));
    expect($r->issues())->toHaveCount(1);
});

test('Combined missing x-default and duplicate', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'/en'], ['hreflang'=>'fr','href'=>'/fr']]));
    // relative warnings (2) + x-default missing (1 suggestion)
    expect($r->warnings())->toHaveCount(2);
    expect($r->suggestions())->toHaveCount(1);
});

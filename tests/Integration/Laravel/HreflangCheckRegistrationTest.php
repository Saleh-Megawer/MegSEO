<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\HreflangCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Registered via facade', function () {
    $e = $this->app->make(Engine::class); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: []));
    expect($r->issues())->toHaveCount(1);
});

test('Score computed', function () {
    $e = $this->app->make(Engine::class); $e->register(new HreflangCheck());
    $r = $e->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com/en']]));
    expect($r->score()->value)->toBeGreaterThan(0);
    expect($r->score()->contributors)->toHaveCount(1);
});

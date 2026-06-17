<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\HreflangCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Deterministic', function () {
    $e = Engine::make(); $e->register(new HreflangCheck());
    $c = new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://x.com/en']], attributes: ['page_url'=>'https://x.com/en','page_language'=>'en']);
    expect($e->analyze($c)->toArray())->toBe($e->analyze($c)->toArray());
});

test('Stable identifier', function () {
    expect((new HreflangCheck())->ref()->id)->toBe('seo.hreflang');
});

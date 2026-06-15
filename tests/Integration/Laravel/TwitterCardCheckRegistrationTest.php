<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\TwitterCardCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Registered via facade', function () {
    $e = $this->app->make(Engine::class); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: []));
    expect($r->issues())->toHaveCount(4);
    expect($r->issues()[0]->sourceCheckId)->toBe('seo.twitter_card');
});

test('Score computed via engine', function () {
    $e = $this->app->make(Engine::class); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['twitter:card'=>'summary','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']));
    expect($r->score()->value)->toBeGreaterThan(0);
    expect($r->score()->contributors)->toHaveCount(1);
});

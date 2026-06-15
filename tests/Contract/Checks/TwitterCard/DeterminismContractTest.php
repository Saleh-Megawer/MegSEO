<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\TwitterCardCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Deterministic repeated runs', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $c = new AnalysisContext(subject: ['twitter:card'=>'summary','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']);
    expect($e->analyze($c)->toArray())->toBe($e->analyze($c)->toArray());
});

test('Stable identifier', function () {
    $c = new TwitterCardCheck();
    expect($c->ref()->id)->toBe('seo.twitter_card');
    expect($c->ref()->id)->toBe($c->ref()->id);
});

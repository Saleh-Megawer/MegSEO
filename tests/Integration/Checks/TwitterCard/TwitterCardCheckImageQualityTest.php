<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\TwitterCardCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Invalid card type produces warning', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['twitter:card'=>'photo','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']));
    expect($r->warnings())->toHaveCount(1);
    expect($r->warnings()[0]->message)->toBe('Invalid twitter:card type');
});

test('Relative image URL produces warning', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['twitter:card'=>'summary','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'/img.jpg']));
    expect($r->warnings())->toHaveCount(1);
    expect($r->warnings()[0]->message)->toBe('twitter:image URL is relative');
});

test('Conflicting values produce suggestion', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['twitter:card'=>['summary','player'],'twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']));
    expect($r->suggestions())->toHaveCount(1);
});

test('Duplicate identical values produce no conflict', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['twitter:card'=>['summary','summary'],'twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']));
    expect($r->suggestions())->toBe([]);
});

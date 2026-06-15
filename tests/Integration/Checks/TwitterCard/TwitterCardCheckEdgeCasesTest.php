<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\TwitterCardCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Whitespace-only twitter:title is empty not missing', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['twitter:card'=>'summary','twitter:title'=>'   ','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']));
    $msgs = array_map(fn($i) => $i->message, $r->issues());
    expect($msgs)->toContain('twitter:title is empty');
    expect($msgs)->not->toContain('twitter:title is missing');
});

test('Empty card suppresses both missing and invalid type', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['twitter:card'=>'','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg']));
    $msgs = array_map(fn($i)=>$i->message, $r->issues());
    expect($msgs)->toContain('twitter:card is empty');
    expect($msgs)->not->toContain('twitter:card is missing');
    expect($r->warnings())->toBe([]); // no type warning for empty
});

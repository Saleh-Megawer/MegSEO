<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\TwitterCardCheck;
use MegSEO\DTO\AnalysisContext;

test('Stable identifier', function () {
    $c = new TwitterCardCheck();
    expect($c->ref()->id)->toBe('seo.twitter_card');
});

test('Valid Twitter Card passes', function () {
    $c = new TwitterCardCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: [
        'twitter:card' => 'summary_large_image', 'twitter:title' => 'Hi', 'twitter:description' => 'D', 'twitter:image' => 'https://x.com/i.jpg',
    ]));
    expect($outcome->issues)->toBe([]);
});

test('Missing twitter:card produces issue', function () {
    $c = new TwitterCardCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: ['twitter:title' => 'Hi', 'twitter:description' => 'D', 'twitter:image' => 'https://x.com/i.jpg']));
    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('twitter:card is missing');
});

test('Empty twitter:card does NOT produce missing issue', function () {
    $c = new TwitterCardCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: ['twitter:card' => '', 'twitter:title' => 'Hi', 'twitter:description' => 'D', 'twitter:image' => 'https://x.com/i.jpg']));
    $msgs = array_map(fn($i) => $i->message, $outcome->issues);
    expect($msgs)->toContain('twitter:card is empty');
    expect($msgs)->not->toContain('twitter:card is missing');
});

test('All missing produces 4 issues', function () {
    $c = new TwitterCardCheck();
    expect($c->analyze(new AnalysisContext(subject: []))->issues)->toHaveCount(4);
});

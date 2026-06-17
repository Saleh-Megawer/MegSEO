<?php

declare(strict_types=1);

use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Checks\Canonical\CanonicalCheck;
use MegSEO\Checks\OpenGraph\OpenGraphCheck;
use MegSEO\Checks\TwitterCard\TwitterCardCheck;
use MegSEO\Checks\Hreflang\HreflangCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Routed single check execution', function () {
    $e = Engine::make();
    $e->register(new TitleCheck());

    $ctx = new AnalysisContext(subject: null, inputs: ['seo.title' => 'Long enough title for validation purposes']);
    $r = $e->analyze($ctx);

    expect($r->issues())->toBe([]);
});

test('Routed multi-check execution — all 6 checks', function () {
    $e = Engine::make();
    $e->register(new TitleCheck());
    $e->register(new MetaDescriptionCheck());
    $e->register(new CanonicalCheck());
    $e->register(new OpenGraphCheck());
    $e->register(new TwitterCardCheck());
    $e->register(new HreflangCheck());

    $ctx = new AnalysisContext(subject: null, inputs: [
        'seo.title'       => 'A Properly Sized Page Title That Passes All Length Thresholds',
        'seo.meta_description' => 'A comprehensive meta description that is long enough to pass the minimum threshold checks for a valid description.',
        'seo.canonical'   => 'https://example.com/page',
        'seo.open_graph'  => ['og:title' => 'OG Title', 'og:description' => 'OG Desc', 'og:image' => 'https://x.com/og.jpg'],
        'seo.twitter_card'=> ['twitter:card' => 'summary', 'twitter:title' => 'TW', 'twitter:description' => 'D', 'twitter:image' => 'https://x.com/tw.jpg'],
        'seo.hreflang'    => [['hreflang' => 'en', 'href' => 'https://x.com/en'], ['hreflang' => 'x-default', 'href' => 'https://x.com/']],
    ]);

    $r = $e->analyze($ctx);

    expect($r->issues())->toBe([]);
    expect($r->warnings())->toBe([]);
    expect($r->suggestions())->toBe([]);
    expect($r->score()->value)->toEqual(600.0);
    expect($r->score()->contributors)->toHaveCount(6);
});

test('Mixed routed + legacy execution', function () {
    $e = Engine::make();
    $e->register(new TitleCheck());
    $e->register(new CanonicalCheck());

    $ctx = new AnalysisContext(
        subject: 'https://example.com/canonical', // CanonicalCheck uses this
        inputs: ['seo.title' => 'Long Enough Page Title For Mixed Mode Testing'], // TitleCheck uses this
    );

    $r = $e->analyze($ctx);
    expect($r->issues())->toBe([]);
});

test('Deterministic routed execution', function () {
    $e = Engine::make();
    $e->register(new TitleCheck());

    $ctx = new AnalysisContext(subject: null, inputs: ['seo.title' => 'A Good Title That Is Long Enough']);

    expect($e->analyze($ctx)->toArray())->toBe($e->analyze($ctx)->toArray());
});

test('Original context unchanged after routing', function () {
    $e = Engine::make();
    $e->register(new TitleCheck());

    $ctx = new AnalysisContext(subject: 'original', inputs: ['seo.title' => 'A Good Title That Is Long Enough']);
    $e->analyze($ctx);

    expect($ctx->subject)->toBe('original'); // not mutated
});

<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\HreflangCheck;
use MegSEO\DTO\AnalysisContext;

test('Stable identifier', function () {
    expect((new HreflangCheck())->ref()->id)->toBe('seo.hreflang');
});

test('Valid entry passes', function () {
    $c = new HreflangCheck();
    $o = $c->analyze(new AnalysisContext(subject: [['hreflang'=>'en','href'=>'https://example.com/en']]));
    expect($o->issues)->toBe([]);
    expect($o->warnings)->toBe([]);
});

test('Missing entries issue', function () {
    $c = new HreflangCheck();
    $o = $c->analyze(new AnalysisContext(subject: []));
    expect($o->issues)->toHaveCount(1);
    expect($o->issues[0]->message)->toBe('Hreflang data is missing');
});

test('Null subject produces missing issue', function () {
    $c = new HreflangCheck();
    $o = $c->analyze(new AnalysisContext(subject: null));
    expect($o->issues)->toHaveCount(1);
});

test('Empty hreflang and empty href flagged separately', function () {
    $c = new HreflangCheck();
    $o = $c->analyze(new AnalysisContext(subject: [['hreflang'=>'','href'=>'']]));
    expect($o->issues)->toHaveCount(2); // both empty
});

test('Invalid language code produces warning', function () {
    $c = new HreflangCheck();
    $o = $c->analyze(new AnalysisContext(subject: [['hreflang'=>'bad!','href'=>'https://x.com']]));
    expect($o->warnings)->toHaveCount(1);
    expect($o->warnings[0]->message)->toBe('Invalid hreflang language code');
});

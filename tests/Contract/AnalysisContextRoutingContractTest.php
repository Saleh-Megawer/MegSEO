<?php

declare(strict_types=1);

use MegSEO\DTO\AnalysisContext;

test('inputFor returns routed input when present', function () {
    $ctx = new AnalysisContext(subject: 'legacy', inputs: ['seo.title' => 'routed title']);
    expect($ctx->inputFor('seo.title'))->toBe('routed title');
});

test('inputFor falls back to subject when no routed input', function () {
    $ctx = new AnalysisContext(subject: 'legacy');
    expect($ctx->inputFor('seo.title'))->toBe('legacy');
});

test('hasInputFor returns true for routed check', function () {
    $ctx = new AnalysisContext(subject: 'legacy', inputs: ['seo.title' => 'value']);
    expect($ctx->hasInputFor('seo.title'))->toBeTrue();
    expect($ctx->hasInputFor('seo.unknown'))->toBeFalse();
});

test('withSubject returns new instance with overridden subject', function () {
    $ctx = new AnalysisContext(subject: 'original', inputs: ['seo.title' => 'val'], requestId: 'req-1');
    $derived = $ctx->withSubject('overridden');

    expect($derived->subject)->toBe('overridden');
    expect($ctx->subject)->toBe('original'); // original unchanged
    expect($derived->inputs)->toBe($ctx->inputs);
    expect($derived->requestId)->toBe('req-1');
    expect($derived)->not->toBe($ctx); // different instances
});

test('withSubject preserves attributes', function () {
    $ctx = new AnalysisContext(subject: 's', attributes: ['key' => 'val']);
    $d = $ctx->withSubject('new');
    expect($d->attributes->get('key'))->toBe('val');
});

test('legacy constructor works unchanged', function () {
    $ctx = new AnalysisContext(subject: 'hello');
    expect($ctx->subject)->toBe('hello');
    expect($ctx->inputs)->toBe([]);
    expect($ctx->inputFor('any'))->toBe('hello');
});

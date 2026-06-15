<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\Rules\EvaluateOgImageUrl;

test('Valid absolute URL returns no findings', function () {
    $r = new EvaluateOgImageUrl();
    expect($r->evaluate('https://example.com/image.jpg'))->toBe([]);
});

test('Relative URL returns warning', function () {
    $r = new EvaluateOgImageUrl();
    $f = $r->evaluate('/images/photo.jpg');
    expect($f)->toHaveCount(1);
    expect($f[0]->message)->toBe('og:image URL is relative');
    expect($f[0]->sourceCheckId)->toBe('seo.open_graph');
});

test('Invalid scheme returns warning', function () {
    $r = new EvaluateOgImageUrl();
    $f = $r->evaluate('ftp://example.com/img.jpg');
    expect($f)->toHaveCount(1);
    expect($f[0]->message)->toBe('og:image URL is invalid');
});

test('Arabic URL accepted', function () {
    $r = new EvaluateOgImageUrl();
    expect($r->evaluate('https://example.com/صورة.jpg'))->toBe([]);
});

test('IDN URL accepted', function () {
    $r = new EvaluateOgImageUrl();
    expect($r->evaluate('https://موقعي.مصر/img.jpg'))->toBe([]);
});

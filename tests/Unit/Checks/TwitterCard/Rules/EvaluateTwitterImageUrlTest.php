<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\Rules\EvaluateTwitterImageUrl;

test('Valid absolute URL returns no findings', function () {
    expect((new EvaluateTwitterImageUrl())->evaluate('https://example.com/img.jpg'))->toBe([]);
});

test('Relative URL returns warning', function () {
    $f = (new EvaluateTwitterImageUrl())->evaluate('/img.jpg');
    expect($f)->toHaveCount(1);
    expect($f[0]->message)->toBe('twitter:image URL is relative');
});

test('Invalid URL returns warning', function () {
    $f = (new EvaluateTwitterImageUrl())->evaluate('ftp://bad.jpg');
    expect($f)->toHaveCount(1);
    expect($f[0]->message)->toBe('twitter:image URL is invalid');
});

test('Arabic URL accepted', function () {
    expect((new EvaluateTwitterImageUrl())->evaluate('https://example.com/صورة.jpg'))->toBe([]);
});

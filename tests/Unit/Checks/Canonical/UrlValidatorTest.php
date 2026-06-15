<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Support\CanonicalUrlValidator;

test('Validates proper https URL', function () {
    $v = new CanonicalUrlValidator();
    expect($v->isValid('https://example.com/page'))->toBeTrue();
    expect($v->isValid('http://example.com'))->toBeTrue();
});

test('Rejects invalid scheme', function () {
    $v = new CanonicalUrlValidator();
    expect($v->isValid('ftp://example.com'))->toBeFalse();
    expect($v->isValid('javascript:void(0)'))->toBeFalse();
    expect($v->isValid('mailto:test@example.com'))->toBeFalse();
});

test('Rejects malformed URL', function () {
    $v = new CanonicalUrlValidator();
    expect($v->isValid('not-a-url'))->toBeFalse();
    expect($v->isValid('https://'))->toBeFalse();
});

test('Detects relative URLs', function () {
    $v = new CanonicalUrlValidator();
    expect($v->isRelative('/page'))->toBeTrue();
    expect($v->isRelative('page.html'))->toBeTrue();
    expect($v->isRelative('https://example.com'))->toBeFalse();
});

test('Accepts Arabic path URLs', function () {
    $v = new CanonicalUrlValidator();
    expect($v->isValid('https://example.com/دليل'))->toBeTrue();
    expect($v->isRelative('https://example.com/دليل'))->toBeFalse();
});

test('Accepts Arabic domain URLs', function () {
    $v = new CanonicalUrlValidator();
    expect($v->isValid('https://موقعي.مصر/صفحة'))->toBeTrue();
});

test('Accepts punycode IDN domains', function () {
    $v = new CanonicalUrlValidator();
    expect($v->isValid('https://xn--mgbh0fb.xn--kgbechtv/'))->toBeTrue();
});

test('Accepts mixed Unicode paths', function () {
    $v = new CanonicalUrlValidator();
    expect($v->isValid('https://example.com/hello-مرحبا-world'))->toBeTrue();
});

test('Rejects non-http schemes with valid URL structure', function () {
    $v = new CanonicalUrlValidator();
    expect($v->isValid('ftp://valid.host/path'))->toBeFalse();
});

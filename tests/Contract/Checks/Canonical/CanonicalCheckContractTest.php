<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\CanonicalCheck;
use MegSEO\DTO\AnalysisContext;

test('Implements Check with stable identifier', function () {
    $c = new CanonicalCheck();
    expect($c->ref()->id)->toBe('seo.canonical');
    expect($c->ref()->label)->toBe('Canonical Check');
});

test('Valid self-referencing canonical passes', function () {
    $c = new CanonicalCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: 'https://example.com/page'));
    expect($outcome->issues)->toBe([]);
});

test('Missing canonical produces issue', function () {
    $c = new CanonicalCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: null));
    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('Canonical tag is missing');
});

test('Empty canonical produces issue', function () {
    $c = new CanonicalCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: ''));
    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('Canonical tag is empty');
});

test('Invalid canonical URL produces issue', function () {
    $c = new CanonicalCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: 'ftp://bad.scheme'));
    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('Canonical URL is invalid');
});

test('Multiple canonicals detected via array input', function () {
    $c = new CanonicalCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: ['canonical' => 'https://ex.com/a', 'canonicals' => ['https://ex.com/a', 'https://ex.com/b']]));
    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('Multiple canonical tags detected');
});

test('Metadata includes stable keys', function () {
    $c = new CanonicalCheck();
    $outcome = $c->analyze(new AnalysisContext(subject: 'https://example.com/page'));
    expect($outcome->metadata)->toHaveKey('checkIdentifier', 'seo.canonical');
    expect($outcome->metadata)->toHaveKey('ruleIdentifiers');
    expect($outcome->metadata)->toHaveKey('normalizationApplied');
});

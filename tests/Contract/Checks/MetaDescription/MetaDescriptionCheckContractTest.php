<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;

test('Implements Check contract with stable identifier', function () {
    $check = new MetaDescriptionCheck();
    $ref = $check->ref();
    expect($ref)->toBeInstanceOf(CheckReference::class);
    expect($ref->id)->toBe('seo.meta_description');
});

test('Returns CheckOutcome for valid description', function () {
    $check = new MetaDescriptionCheck();
    $desc = str_repeat('A valid meta description with enough characters to pass all checks and thresholds. ', 2);
    $outcome = $check->analyze(new AnalysisContext(subject: $desc));
    expect($outcome)->toBeInstanceOf(CheckOutcome::class);
    expect($outcome->issues)->toBe([]);
    expect($outcome->warnings)->toBe([]);
});

test('Returns issue for missing description', function () {
    $check = new MetaDescriptionCheck();
    $outcome = $check->analyze(new AnalysisContext(subject: null));
    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('Meta description is missing');
});

test('Returns issue for empty description', function () {
    $check = new MetaDescriptionCheck();
    $outcome = $check->analyze(new AnalysisContext(subject: ''));
    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('Meta description is empty');
});

test('Returns issue for separator-only description', function () {
    $check = new MetaDescriptionCheck();
    $outcome = $check->analyze(new AnalysisContext(subject: '---===___'));
    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toContain('punctuation');
});

test('Returns warning for short description', function () {
    $check = new MetaDescriptionCheck();
    $outcome = $check->analyze(new AnalysisContext(subject: 'Short desc'));
    expect($outcome->warnings)->toHaveCount(1);
    expect($outcome->warnings[0]->message)->toBe('Meta description is too short');
});

test('Returns warning for long description', function () {
    $check = new MetaDescriptionCheck();
    $outcome = $check->analyze(new AnalysisContext(subject: str_repeat('a', 200)));
    expect($outcome->warnings)->toHaveCount(1);
    expect($outcome->warnings[0]->message)->toBe('Meta description is too long');
});

test('Metadata includes stable identifiers', function () {
    $check = new MetaDescriptionCheck();
    $desc = str_repeat('A valid meta description with enough characters to pass all checks and thresholds. ', 2);
    $outcome = $check->analyze(new AnalysisContext(subject: $desc));
    expect($outcome->metadata)->toHaveKey('checkIdentifier', 'seo.meta_description');
    expect($outcome->metadata)->toHaveKey('ruleIdentifiers');
    expect($outcome->metadata)->toHaveKey('normalizedLength');
});

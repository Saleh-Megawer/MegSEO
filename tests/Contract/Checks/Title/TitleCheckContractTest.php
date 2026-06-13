<?php

declare(strict_types=1);

use MegSEO\Checks\Title\TitleCheck;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\Support\ImmutableMap;

test('TitleCheck implements Check contract with stable identifier', function () {
    $check = new TitleCheck();
    $ref = $check->ref();

    expect($ref)->toBeInstanceOf(CheckReference::class);
    expect($ref->id)->toBe('seo.title');
    expect($ref->label)->toBe('Title Check');
});

test('TitleCheck returns CheckOutcome for valid title', function () {
    $check = new TitleCheck();
    $context = new AnalysisContext(subject: 'A Comprehensive Guide to Effective Page Title Optimization');

    $outcome = $check->analyze($context);

    expect($outcome)->toBeInstanceOf(CheckOutcome::class);
    expect($outcome->issues)->toBe([]);
    expect($outcome->warnings)->toBe([]);
    expect($outcome->suggestions)->toBe([]);
});

test('TitleCheck returns issue for missing title', function () {
    $check = new TitleCheck();
    $context = new AnalysisContext(subject: null);

    $outcome = $check->analyze($context);

    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('Title is missing');
});

test('TitleCheck returns issue for empty title', function () {
    $check = new TitleCheck();
    $context = new AnalysisContext(subject: '');

    $outcome = $check->analyze($context);

    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('Title is empty');
});

test('TitleCheck returns issue for whitespace-only title', function () {
    $check = new TitleCheck();
    $context = new AnalysisContext(subject: '     ');

    $outcome = $check->analyze($context);

    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toBe('Title is empty');
});

test('TitleCheck returns issue for separator-only title', function () {
    $check = new TitleCheck();
    $context = new AnalysisContext(subject: '---===___');

    $outcome = $check->analyze($context);

    expect($outcome->issues)->toHaveCount(1);
    expect($outcome->issues[0]->message)->toContain('punctuation');
});

test('TitleCheck returns warning for short title', function () {
    $check = new TitleCheck();
    $context = new AnalysisContext(subject: 'Short');

    $outcome = $check->analyze($context);

    expect($outcome->warnings)->toHaveCount(1);
    expect($outcome->warnings[0]->message)->toBe('Title is too short');
});

test('TitleCheck returns warning for long title', function () {
    $check = new TitleCheck();
    $longTitle = str_repeat('a', 100);
    $context = new AnalysisContext(subject: $longTitle);

    $outcome = $check->analyze($context);

    expect($outcome->warnings)->toHaveCount(1);
    expect($outcome->warnings[0]->message)->toBe('Title is too long');
});

test('TitleCheck metadata includes stable identifiers', function () {
    $check = new TitleCheck();
    $context = new AnalysisContext(subject: 'My Title');

    $outcome = $check->analyze($context);

    expect($outcome->metadata)->toHaveKey('checkIdentifier', 'seo.title');
    expect($outcome->metadata)->toHaveKey('ruleIdentifiers');
    expect($outcome->metadata)->toHaveKey('normalizedLength');
    expect($outcome->metadata)->toHaveKey('focusKeywordSupplied');
    expect($outcome->metadata['normalizedLength'])->toBe(8);
});

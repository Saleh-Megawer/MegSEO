<?php

declare(strict_types=1);

use MegSEO\Checks\Title\DTO\TitleCheckInput;
use MegSEO\Checks\Title\DTO\TitleNormalizationResult;
use MegSEO\Checks\Title\DTO\TitleDuplicateMatch;
use MegSEO\Checks\Title\DTO\TitleCheckMetadata;

test('TitleCheckInput is immutable and stores values', function () {
    $input = new TitleCheckInput(title: 'Hello World', focusKeyword: 'hello');

    expect($input->title)->toBe('Hello World');
    expect($input->focusKeyword)->toBe('hello');
    expect($input->hasTitle())->toBeTrue();
    expect($input->hasFocusKeyword())->toBeTrue();
    expect($input->hasDuplicateSupportData())->toBeFalse();
});

test('TitleCheckInput handles missing title data safely', function () {
    $input = new TitleCheckInput(title: null);

    expect($input->title)->toBeNull();
    expect($input->hasTitle())->toBeFalse();
    expect($input->hasFocusKeyword())->toBeFalse();
});

test('TitleCheckInput with duplicate support data', function () {
    $input = new TitleCheckInput(
        title: 'Test',
        duplicateSupportData: ['page_a' => 'Test'],
    );

    expect($input->hasDuplicateSupportData())->toBeTrue();
});

test('TitleNormalizationResult stores raw and normalized values', function () {
    $result = new TitleNormalizationResult(
        rawTitle: '  Hello  World  ',
        normalizedTitle: 'Hello World',
    );

    expect($result->rawTitle)->toBe('  Hello  World  ');
    expect($result->normalizedTitle)->toBe('Hello World');
    expect($result->isNormalized())->toBeTrue();
});

test('TitleNormalizationResult with matching raw and normalized', function () {
    $result = new TitleNormalizationResult(
        rawTitle: 'Hello World',
        normalizedTitle: 'Hello World',
    );

    expect($result->isNormalized())->toBeFalse();
});

test('TitleDuplicateMatch stores match data', function () {
    $match = new TitleDuplicateMatch(
        matchedTitle: 'Duplicate Title',
        matchedReference: '/about',
        matchReason: 'exact',
    );

    expect($match->matchedTitle)->toBe('Duplicate Title');
    expect($match->matchedReference)->toBe('/about');
    expect($match->matchReason)->toBe('exact');
});

test('TitleCheckMetadata stores stable metadata', function () {
    $metadata = new TitleCheckMetadata(
        checkIdentifier: 'seo.title',
        rawTitle: 'My Title',
        normalizedTitle: 'My Title',
        normalizedLength: 8,
        duplicateSupportUsed: false,
        focusKeywordSupplied: true,
        ruleIdentifiers: ['detect-missing-title', 'detect-empty-title'],
    );

    expect($metadata->checkIdentifier)->toBe('seo.title');
    expect($metadata->rawTitle)->toBe('My Title');
    expect($metadata->normalizedLength)->toBe(8);
    expect($metadata->duplicateSupportUsed)->toBeFalse();
    expect($metadata->focusKeywordSupplied)->toBeTrue();
    expect($metadata->ruleIdentifiers)->toBe(['detect-missing-title', 'detect-empty-title']);
});

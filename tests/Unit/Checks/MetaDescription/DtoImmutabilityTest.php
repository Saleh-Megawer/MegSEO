<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionCheckInput;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionDuplicateMatch;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionCheckMetadata;

test('MetaDescriptionCheckInput is immutable and stores values', function () {
    $input = new MetaDescriptionCheckInput(description: 'A page description', focusKeyword: 'SEO');

    expect($input->description)->toBe('A page description');
    expect($input->focusKeyword)->toBe('SEO');
    expect($input->hasDescription())->toBeTrue();
    expect($input->hasFocusKeyword())->toBeTrue();
    expect($input->hasDuplicateSupportData())->toBeFalse();
});

test('MetaDescriptionCheckInput handles missing data safely', function () {
    $input = new MetaDescriptionCheckInput(description: null);

    expect($input->description)->toBeNull();
    expect($input->hasDescription())->toBeFalse();
});

test('MetaDescriptionNormalizationResult stores raw and normalized values', function () {
    $result = new MetaDescriptionNormalizationResult(
        rawDescription: '  Description  text  ',
        normalizedDescription: 'Description text',
    );

    expect($result->rawDescription)->toBe('  Description  text  ');
    expect($result->normalizedDescription)->toBe('Description text');
    expect($result->isNormalized())->toBeTrue();
});

test('MetaDescriptionDuplicateMatch stores match data', function () {
    $match = new MetaDescriptionDuplicateMatch('Duplicate Desc', '/page', 'exact');

    expect($match->matchedDescription)->toBe('Duplicate Desc');
    expect($match->matchedReference)->toBe('/page');
    expect($match->matchReason)->toBe('exact');
});

test('MetaDescriptionCheckMetadata stores stable metadata', function () {
    $metadata = new MetaDescriptionCheckMetadata(
        checkIdentifier: 'seo.meta_description',
        normalizedLength: 140,
        focusKeywordSupplied: true,
        ruleIdentifiers: ['detect-missing-description'],
    );

    expect($metadata->checkIdentifier)->toBe('seo.meta_description');
    expect($metadata->normalizedLength)->toBe(140);
    expect($metadata->focusKeywordSupplied)->toBeTrue();
    expect($metadata->ruleIdentifiers)->toBe(['detect-missing-description']);
});

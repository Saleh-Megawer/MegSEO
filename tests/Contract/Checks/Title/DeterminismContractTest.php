<?php

declare(strict_types=1);

use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckReference;

test('Deterministic repeated runs produce identical CheckOutcome', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'Welcome to Our Platform',
        attributes: [
            'focus_keyword' => 'Platform',
            'duplicate_support_data' => [
                ['title' => 'Welcome to Our Platform', 'reference' => '/other'],
            ],
        ],
    );

    $result1 = $engine->analyze($context);
    $result2 = $engine->analyze($context);

    expect($result1->toArray())->toBe($result2->toArray());
});

test('Stable check identifier is consistent across runs', function () {
    $check = new TitleCheck();
    $ref1 = $check->ref();
    $ref2 = $check->ref();

    expect($ref1)->toBeInstanceOf(CheckReference::class);
    expect($ref1->id)->toBe('seo.title');
    expect($ref1->id)->toBe($ref2->id);
    expect($ref1->label)->toBe($ref2->label);
    expect($ref1->version)->toBe($ref2->version);
});

test('Metadata remains stable across identical runs', function () {
    $check = new TitleCheck();

    $context = new AnalysisContext(
        subject: 'A Unique Page Title Description Here',
        attributes: ['focus_keyword' => 'Title'],
    );

    $outcome1 = $check->analyze($context);
    $outcome2 = $check->analyze($context);

    expect($outcome1->metadata)->toBe($outcome2->metadata);
});

test('Score contribution is deterministic', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: 'A Valid Short Title'); // <= triggers short warning
    $result1 = $engine->analyze($context);
    $result2 = $engine->analyze($context);

    expect($result1->score()->value)->toBe($result2->score()->value);
    expect($result1->score()->contributors)->toBe($result2->score()->contributors);
    expect($result1->score()->metadata)->toBe($result2->score()->metadata);
});

test('Metadata includes required keys at CheckOutcome level', function () {
    $check = new TitleCheck();

    $context = new AnalysisContext(
        subject: 'A Title Description That Is Long Enough To Pass',
        attributes: ['focus_keyword' => 'Title'],
    );

    $outcome = $check->analyze($context);

    expect($outcome->metadata)->toHaveKey('checkIdentifier', 'seo.title');
    expect($outcome->metadata)->toHaveKey('ruleIdentifiers');
    expect($outcome->metadata)->toHaveKey('normalizedLength');
    expect($outcome->metadata)->toHaveKey('duplicateSupportUsed');
    expect($outcome->metadata)->toHaveKey('focusKeywordSupplied');
    expect($outcome->metadata)->toHaveKey('containsFocusKeyword');
    expect($outcome->metadata)->toHaveKey('duplicateMatchesCount');
    expect($outcome->metadata)->toHaveKey('normalizationApplied');
});

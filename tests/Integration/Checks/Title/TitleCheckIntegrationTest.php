<?php

declare(strict_types=1);

use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Full US1 integration through MegSEO engine — valid title passes', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: 'A Comprehensive Guide to Effective Page Title Optimization');
    $result = $engine->analyze($context);

    expect($result->issues())->toBe([]);
    expect($result->warnings())->toBe([]);
});

test('Full US1 integration through MegSEO engine — missing title produces issue', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: null);
    $result = $engine->analyze($context);

    expect($result->issues())->toHaveCount(1);
    expect($result->issues()[0]->message)->toBe('Title is missing');
});

test('Full US1 integration through MegSEO engine — empty title produces issue', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: '');
    $result = $engine->analyze($context);

    expect($result->issues())->toHaveCount(1);
    expect($result->issues()[0]->message)->toBe('Title is empty');
});

test('Full US1 integration through MegSEO engine — separator-only title produces issue', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: '!!!...???');
    $result = $engine->analyze($context);

    expect($result->issues())->toHaveCount(1);
    expect($result->issues()[0]->message)->toContain('punctuation');
});

test('Full US1 integration through MegSEO engine — short title produces warning', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: 'Short');
    $result = $engine->analyze($context);

    expect($result->warnings())->toHaveCount(1);
    expect($result->warnings()[0]->message)->toBe('Title is too short');
});

test('Full US1 integration through MegSEO engine — long title produces warning', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $longTitle = str_repeat('a', 100);
    $context = new AnalysisContext(subject: $longTitle);
    $result = $engine->analyze($context);

    expect($result->warnings())->toHaveCount(1);
    expect($result->warnings()[0]->message)->toBe('Title is too long');
});

test('Full US1 integration — deterministic repeated runs produce identical outputs', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: 'My Consistent Title');
    $result1 = $engine->analyze($context);
    $result2 = $engine->analyze($context);

    expect($result1->toArray())->toBe($result2->toArray());
});

test('Full US1 integration — title from attributes array', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: ['title' => 'This Is a Properly Sized Page Title Here']);
    $result = $engine->analyze($context);

    expect($result->issues())->toBe([]);
    expect($result->warnings())->toBe([]);
});

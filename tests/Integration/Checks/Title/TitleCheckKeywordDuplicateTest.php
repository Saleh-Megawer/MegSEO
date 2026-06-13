<?php

declare(strict_types=1);

use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

// ── Focus Keyword Scenarios ───────────────────────────────────

test('Focus keyword present — no suggestion emitted', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'Welcome to MegSEO — The Ultimate SEO Intelligence Engine',
        attributes: ['focus_keyword' => 'MegSEO'],
    );

    $result = $engine->analyze($context);

    expect($result->issues())->toBe([]);
    expect($result->warnings())->toBe([]);
    expect($result->suggestions())->toBe([]);
});

test('Focus keyword absent — suggestion emitted', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'Welcome to Our Platform',
        attributes: ['focus_keyword' => 'MegSEO'],
    );

    $result = $engine->analyze($context);

    expect($result->suggestions())->toHaveCount(1);
    expect($result->suggestions()[0]->message)->toContain('Focus keyword');
    expect($result->suggestions()[0]->sourceCheckId)->toBe('seo.title');
});

test('No focus keyword supplied — no suggestion emitted', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'Welcome to Our Platform',
    );

    $result = $engine->analyze($context);

    expect($result->suggestions())->toBe([]);
});

test('Arabic focus keyword present — no suggestion', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'مرحباً بكم في منصة MegSEO',
        attributes: ['focus_keyword' => 'MegSEO'],
    );

    $result = $engine->analyze($context);

    expect($result->suggestions())->toBe([]);
});

test('Arabic focus keyword absent — suggestion emitted', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'منصة تحسين المحتوى',
        attributes: ['focus_keyword' => 'MegSEO'],
    );

    $result = $engine->analyze($context);

    expect($result->suggestions())->toHaveCount(1);
    expect($result->suggestions()[0]->message)->toContain('Focus keyword');
});

test('Case insensitive keyword match works end-to-end', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'Welcome to MEGSEO Platform',
        attributes: ['focus_keyword' => 'megseo'],
    );

    $result = $engine->analyze($context);

    expect($result->suggestions())->toBe([]);
});

// ── Duplicate Title Scenarios ─────────────────────────────────

test('No duplicate support data — degrades gracefully', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'My Page Title',
    );

    $result = $engine->analyze($context);

    expect($result->failures)->toBe([]);
    expect($result->suggestions())->toBe([]);
});

test('Duplicate support data with no match — no suggestion', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'Unique Title Here',
        attributes: [
            'duplicate_support_data' => [
                ['title' => 'Other Page', 'reference' => '/other'],
            ],
        ],
    );

    $result = $engine->analyze($context);

    expect($result->suggestions())->toBe([]);
});

test('Duplicate support data with match — suggestion emitted', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'My Page Title',
        attributes: [
            'duplicate_support_data' => [
                ['title' => 'My Page Title', 'reference' => '/other-page'],
            ],
        ],
    );

    $result = $engine->analyze($context);

    expect($result->suggestions())->toHaveCount(1);
    expect($result->suggestions()[0]->message)->toContain('Duplicate');
    expect($result->suggestions()[0]->sourceCheckId)->toBe('seo.title');
});

// ── Deterministic Repeated Runs ───────────────────────────────

test('Deterministic repeated runs with keyword and duplicate data', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'My Page Title',
        attributes: [
            'focus_keyword' => 'MegSEO',
            'duplicate_support_data' => [
                ['title' => 'My Page Title', 'reference' => '/other-page'],
            ],
        ],
    );

    $result1 = $engine->analyze($context);
    $result2 = $engine->analyze($context);

    expect($result1->toArray())->toBe($result2->toArray());
});

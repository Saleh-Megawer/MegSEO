<?php

declare(strict_types=1);

use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Arabic title with valid length passes without findings', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'مرحباً بكم في موقعنا الإلكتروني لتحسين محركات البحث',
    );

    $result = $engine->analyze($context);

    expect($result->issues())->toBe([]);
    expect($result->warnings())->toBe([]);
});

test('Arabic short title produces appropriate warning', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: 'الرئيسية');

    $result = $engine->analyze($context);

    expect($result->warnings())->toHaveCount(1);
    expect($result->warnings()[0]->sourceCheckId)->toBe('seo.title');
});

test('Arabic title with focus keyword match works', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'مرحباً بكم في منصة MegSEO لتحسين المحتوى',
        attributes: ['focus_keyword' => 'MegSEO'],
    );

    $result = $engine->analyze($context);

    expect($result->suggestions())->toBe([]);
});

test('Arabic title with missing focus keyword emits suggestion', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'منصة تحسين المحتوى العربي',
        attributes: ['focus_keyword' => 'MegSEO'],
    );

    $result = $engine->analyze($context);

    expect($result->suggestions())->toHaveCount(1);
});

test('Mixed Arabic-English title handles normalization', function () {
    $check = new TitleCheck();

    $context = new AnalysisContext(
        subject: '  مرحباً   World   منصة  MegSEO  ',
    );

    $outcome = $check->analyze($context);

    expect($outcome->metadata['normalizationApplied'])->toBeTrue();
});

test('Unicode special characters are handled gracefully', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(subject: 'Café — The Best Coffee ☕ in Town™');

    $result = $engine->analyze($context);

    expect($result->issues())->toBe([]);
});

test('Arabic duplicate title detected', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'عنوان الصفحة الرئيسية',
        attributes: [
            'duplicate_support_data' => [
                ['title' => 'عنوان الصفحة الرئيسية', 'reference' => '/home'],
            ],
        ],
    );

    $result = $engine->analyze($context);

    expect($result->suggestions())->toHaveCount(1);
    expect($result->suggestions()[0]->message)->toContain('Duplicate');
});

test('Deterministic repeated runs with Arabic content produce identical output', function () {
    $engine = Engine::make();
    $engine->register(new TitleCheck());

    $context = new AnalysisContext(
        subject: 'مرحباً بكم في موقعنا الإلكتروني لتحسين محركات البحث',
        attributes: ['focus_keyword' => 'محركات'],
    );

    $result1 = $engine->analyze($context);
    $result2 = $engine->analyze($context);

    expect($result1->toArray())->toBe($result2->toArray());
});

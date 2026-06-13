<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Focus keyword present — no suggestion', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = str_repeat('A valid meta description with enough characters to pass all checks and thresholds. ', 2);
    $c = new AnalysisContext(subject: $desc, attributes: ['focus_keyword' => 'meta']);
    $r = $engine->analyze($c);
    expect($r->suggestions())->toBe([]);
});

test('Focus keyword absent — suggestion emitted', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = str_repeat('A valid meta description with enough characters to pass all checks and thresholds. ', 2);
    $c = new AnalysisContext(subject: $desc, attributes: ['focus_keyword' => 'MegSEO']);
    $r = $engine->analyze($c);
    expect($r->suggestions())->toHaveCount(1);
    expect($r->suggestions()[0]->message)->toContain('Focus keyword');
});

test('No keyword supplied — no suggestion', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = str_repeat('A valid meta description with enough characters to pass all checks and thresholds. ', 2);
    $r = $engine->analyze(new AnalysisContext(subject: $desc));
    expect($r->suggestions())->toBe([]);
});

test('Duplicate support data degrades gracefully when absent', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = str_repeat('A valid meta description with enough characters to pass all checks and thresholds. ', 2);
    $r = $engine->analyze(new AnalysisContext(subject: $desc));
    expect($r->failures)->toBe([]);
});

test('Duplicate match — suggestion emitted', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = str_repeat('A description that is long enough and matches another page title text here. ', 2);
    $c = new AnalysisContext(
        subject: $desc,
        attributes: ['duplicate_support_data' => [['title' => $desc, 'reference' => '/other']]],
    );
    $r = $engine->analyze($c);
    expect($r->suggestions())->toHaveCount(1);
    expect($r->suggestions()[0]->message)->toContain('Duplicate');
});

test('Deterministic repeated runs with keyword and duplicate', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = str_repeat('A description that is long enough and matches another page title text here. ', 2);
    $c = new AnalysisContext(
        subject: $desc,
        attributes: ['focus_keyword' => 'MegSEO', 'duplicate_support_data' => [['title' => $desc, 'reference' => '/other']]],
    );
    expect($engine->analyze($c)->toArray())->toBe($engine->analyze($c)->toArray());
});

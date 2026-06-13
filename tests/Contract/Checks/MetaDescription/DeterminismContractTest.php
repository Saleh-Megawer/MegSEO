<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckReference;

test('Deterministic repeated runs produce identical output', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = str_repeat('A long valid description for determinism testing with keyword data and checks. ', 2);
    $c = new AnalysisContext(subject: $desc, attributes: ['focus_keyword' => 'keyword', 'duplicate_support_data' => [['title' => $desc, 'reference' => '/other']]]);
    expect($engine->analyze($c)->toArray())->toBe($engine->analyze($c)->toArray());
});

test('Stable identifier', function () {
    $c = new MetaDescriptionCheck();
    $r1 = $c->ref();
    $r2 = $c->ref();
    expect($r1)->toBeInstanceOf(CheckReference::class);
    expect($r1->id)->toBe('seo.meta_description');
    expect($r2->id)->toBe($r1->id);
});

test('Metadata stability', function () {
    $c = new MetaDescriptionCheck();
    $desc = str_repeat('A valid description for metadata stability testing purposes with extra text. ', 2);
    $ctx = new AnalysisContext(subject: $desc, attributes: ['focus_keyword' => 'description']);
    expect($c->analyze($ctx)->metadata)->toBe($c->analyze($ctx)->metadata);
});

test('Score determinism', function () {
    $c = new MetaDescriptionCheck();
    $ctx = new AnalysisContext(subject: 'Too short');
    $s1 = $c->analyze($ctx)->scoreContribution;
    $s2 = $c->analyze($ctx)->scoreContribution;
    expect($s1->value)->toBe($s2->value);
});

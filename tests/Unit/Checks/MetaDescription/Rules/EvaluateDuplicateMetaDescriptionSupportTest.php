<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Rules\EvaluateDuplicateMetaDescriptionSupport;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionDuplicateMatch;

test('Returns null when no duplicates', function () {
    $rule = new EvaluateDuplicateMetaDescriptionSupport();
    expect($rule->evaluate('My Unique Description', []))->toBeNull();
});

test('Returns suggestion when duplicate detected', function () {
    $rule = new EvaluateDuplicateMetaDescriptionSupport();
    $matches = [new MetaDescriptionDuplicateMatch('My Desc', '/other-page', 'exact')];
    $s = $rule->evaluate('My Desc', $matches);
    expect($s)->not->toBeNull();
    expect($s->message)->toContain('Duplicate');
    expect($s->sourceCheckId)->toBe('seo.meta_description');
});

test('Includes reference in details', function () {
    $rule = new EvaluateDuplicateMetaDescriptionSupport();
    $matches = [new MetaDescriptionDuplicateMatch('About Us Desc', '/about', 'exact')];
    $s = $rule->evaluate('About Us Desc', $matches);
    expect($s->details)->toContain('/about');
});

test('Handles multiple duplicates', function () {
    $rule = new EvaluateDuplicateMetaDescriptionSupport();
    $matches = [new MetaDescriptionDuplicateMatch('Desc', '/p1', 'exact'), new MetaDescriptionDuplicateMatch('Desc', '/p2', 'exact')];
    $s = $rule->evaluate('Desc', $matches);
    expect($s->message)->toContain('2');
});

test('Deterministic', function () {
    $rule = new EvaluateDuplicateMetaDescriptionSupport();
    $matches = [new MetaDescriptionDuplicateMatch('T', '/r', 'exact')];
    $r1 = $rule->evaluate('T', $matches);
    $r2 = $rule->evaluate('T', $matches);
    expect($r1?->message)->toBe($r2?->message);
});

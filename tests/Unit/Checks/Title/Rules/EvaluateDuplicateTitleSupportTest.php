<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Rules\EvaluateDuplicateTitleSupport;
use MegSEO\Checks\Title\DTO\TitleDuplicateMatch;

test('EvaluateDuplicateTitleSupport returns null when no duplicates', function () {
    $rule = new EvaluateDuplicateTitleSupport();
    $suggestion = $rule->evaluate('My Unique Title', []);

    expect($suggestion)->toBeNull();
});

test('EvaluateDuplicateTitleSupport returns null when empty matches array', function () {
    $rule = new EvaluateDuplicateTitleSupport();
    $suggestion = $rule->evaluate('Some Title', []);

    expect($suggestion)->toBeNull();
});

test('EvaluateDuplicateTitleSupport returns suggestion when duplicate detected', function () {
    $rule = new EvaluateDuplicateTitleSupport();
    $matches = [
        new TitleDuplicateMatch(
            matchedTitle: 'My Page Title',
            matchedReference: '/other-page',
            matchReason: 'exact',
        ),
    ];

    $suggestion = $rule->evaluate('My Page Title', $matches);

    expect($suggestion)->not->toBeNull();
    expect($suggestion->message)->toContain('Duplicate');
    expect($suggestion->sourceCheckId)->toBe('seo.title');
});

test('EvaluateDuplicateTitleSupport includes reference details in suggestion', function () {
    $rule = new EvaluateDuplicateTitleSupport();
    $matches = [
        new TitleDuplicateMatch(
            matchedTitle: 'About Us',
            matchedReference: '/about',
            matchReason: 'exact',
        ),
    ];

    $suggestion = $rule->evaluate('About Us', $matches);

    expect($suggestion)->not->toBeNull();
    expect($suggestion->details)->toContain('/about');
});

test('EvaluateDuplicateTitleSupport handles multiple duplicates', function () {
    $rule = new EvaluateDuplicateTitleSupport();
    $matches = [
        new TitleDuplicateMatch('Title', '/page1', 'exact'),
        new TitleDuplicateMatch('Title', '/page2', 'exact'),
    ];

    $suggestion = $rule->evaluate('Title', $matches);

    expect($suggestion)->not->toBeNull();
    expect($suggestion->message)->toContain('2');
});

test('EvaluateDuplicateTitleSupport is deterministic', function () {
    $rule = new EvaluateDuplicateTitleSupport();
    $matches = [
        new TitleDuplicateMatch('Test', '/ref', 'exact'),
    ];

    $result1 = $rule->evaluate('Test', $matches);
    $result2 = $rule->evaluate('Test', $matches);

    expect($result1?->message)->toBe($result2?->message);
    expect($result1?->details)->toBe($result2?->details);
});

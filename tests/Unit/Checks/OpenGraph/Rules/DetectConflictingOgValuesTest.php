<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\Rules\DetectConflictingOgValues;
use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;

test('No conflict with single value', function () {
    $r = new DetectConflictingOgValues();
    $input = new OpenGraphCheckInput(['og:title' => 'Hello']);
    expect($r->evaluate($input))->toBe([]);
});

test('No conflict with duplicate identical values', function () {
    $r = new DetectConflictingOgValues();
    $input = new OpenGraphCheckInput(['og:title' => ['Article', 'Article']]);
    expect($r->evaluate($input))->toBe([]);
});

test('Conflict detected with different values', function () {
    $r = new DetectConflictingOgValues();
    $input = new OpenGraphCheckInput(['og:title' => ['Article A', 'Article B']]);
    $s = $r->evaluate($input);
    expect($s)->toHaveCount(1);
    expect($s[0]->message)->toContain('Conflicting og:title');
    expect($s[0]->sourceCheckId)->toBe('seo.open_graph');
});

test('Conflict on og:image', function () {
    $r = new DetectConflictingOgValues();
    $input = new OpenGraphCheckInput(['og:image' => ['a.jpg', 'b.jpg']]);
    $s = $r->evaluate($input);
    expect($s)->toHaveCount(1);
    expect($s[0]->message)->toContain('Conflicting og:image');
});

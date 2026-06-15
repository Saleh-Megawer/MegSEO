<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\Rules\DetectEmptyOgValues;
use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;

test('Returns issues for empty og values', function () {
    $r = new DetectEmptyOgValues();
    $input = new OpenGraphCheckInput(['og:title' => '', 'og:description' => '', 'og:image' => '']);
    $issues = $r->evaluate($input);
    expect($issues)->toHaveCount(3);
    expect($issues[0]->message)->toBe('og:title is empty');
});

test('Returns no issues when all values are present', function () {
    $r = new DetectEmptyOgValues();
    $input = new OpenGraphCheckInput(['og:title' => 'Hello', 'og:description' => 'Desc', 'og:image' => 'https://img.jpg']);
    expect($r->evaluate($input))->toBe([]);
});

test('Whitespace-only values are considered empty', function () {
    $r = new DetectEmptyOgValues();
    $input = new OpenGraphCheckInput(['og:title' => '   ']);
    $issues = $r->evaluate($input);
    expect($issues)->toHaveCount(1);
});

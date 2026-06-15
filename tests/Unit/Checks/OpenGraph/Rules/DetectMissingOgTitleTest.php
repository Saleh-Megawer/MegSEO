<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\Rules\DetectMissingOgTitle;
use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;

test('Returns issue when og:title is missing', function () {
    $r = new DetectMissingOgTitle();
    $input = new OpenGraphCheckInput([]);
    $issue = $r->evaluate($input, false);
    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('og:title is missing');
});

test('Returns null when og:title is present', function () {
    $r = new DetectMissingOgTitle();
    $input = new OpenGraphCheckInput(['og:title' => 'Hello']);
    expect($r->evaluate($input, false))->toBeNull();
});

test('Returns null when empty detected (suppressed)', function () {
    $r = new DetectMissingOgTitle();
    $input = new OpenGraphCheckInput(['og:title' => '']);
    expect($r->evaluate($input, true))->toBeNull();
});

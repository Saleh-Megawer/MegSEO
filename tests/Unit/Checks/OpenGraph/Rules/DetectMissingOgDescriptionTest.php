<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\Rules\DetectMissingOgDescription;
use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;

test('Returns issue when og:description is missing', function () {
    $r = new DetectMissingOgDescription();
    $input = new OpenGraphCheckInput([]);
    $issue = $r->evaluate($input, false);
    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('og:description is missing');
});

test('Returns null when present', function () {
    $r = new DetectMissingOgDescription();
    $input = new OpenGraphCheckInput(['og:description' => 'Desc']);
    expect($r->evaluate($input, false))->toBeNull();
});

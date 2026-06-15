<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\Rules\DetectMissingOgImage;
use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;

test('Returns issue when og:image is missing', function () {
    $r = new DetectMissingOgImage();
    $input = new OpenGraphCheckInput([]);
    $issue = $r->evaluate($input, false);
    expect($issue)->not->toBeNull();
    expect($issue->message)->toBe('og:image is missing');
});

test('Returns null when present', function () {
    $r = new DetectMissingOgImage();
    $input = new OpenGraphCheckInput(['og:image' => 'https://img.jpg']);
    expect($r->evaluate($input, false))->toBeNull();
});

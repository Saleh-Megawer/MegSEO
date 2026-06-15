<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\Rules\DetectEmptyTwitterValues;
use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;

test('Returns issues for all empty values', function () {
    $r = new DetectEmptyTwitterValues();
    $input = new TwitterCardCheckInput(['twitter:card' => '', 'twitter:title' => '', 'twitter:description' => '', 'twitter:image' => '']);
    $issues = $r->evaluate($input);
    expect($issues)->toHaveCount(4);
    expect($issues[0]->message)->toBe('twitter:card is empty');
});

test('Returns no issues when all present', function () {
    $r = new DetectEmptyTwitterValues();
    $input = new TwitterCardCheckInput(['twitter:card' => 'summary', 'twitter:title' => 'Hi', 'twitter:description' => 'D', 'twitter:image' => 'https://x.com/i.jpg']);
    expect($r->evaluate($input))->toBe([]);
});

test('Whitespace-only is empty', function () {
    $r = new DetectEmptyTwitterValues();
    $input = new TwitterCardCheckInput(['twitter:title' => '   ']);
    expect($r->evaluate($input))->toHaveCount(1);
});

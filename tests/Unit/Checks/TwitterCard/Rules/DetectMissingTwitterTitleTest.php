<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\Rules\DetectMissingTwitterTitle;
use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;

test('Returns issue when twitter:title missing', function () {
    $r = new DetectMissingTwitterTitle();
    expect($r->evaluate(new TwitterCardCheckInput([]), false))->not->toBeNull();
});

test('Returns null when present', function () {
    $r = new DetectMissingTwitterTitle();
    expect($r->evaluate(new TwitterCardCheckInput(['twitter:title' => 'Hi']), false))->toBeNull();
});

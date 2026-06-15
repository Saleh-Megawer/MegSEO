<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\Rules\DetectMissingTwitterImage;
use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;

test('Returns issue when twitter:image missing', function () {
    $r = new DetectMissingTwitterImage();
    expect($r->evaluate(new TwitterCardCheckInput([]), false))->not->toBeNull();
});

test('Returns null when present', function () {
    $r = new DetectMissingTwitterImage();
    expect($r->evaluate(new TwitterCardCheckInput(['twitter:image' => 'https://x.com/i.jpg']), false))->toBeNull();
});

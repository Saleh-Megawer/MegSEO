<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\Rules\DetectMissingTwitterCard;
use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;

test('Returns issue when twitter:card missing', function () {
    $r = new DetectMissingTwitterCard();
    expect($r->evaluate(new TwitterCardCheckInput([]), false))->not->toBeNull();
});

test('Returns null when present', function () {
    $r = new DetectMissingTwitterCard();
    expect($r->evaluate(new TwitterCardCheckInput(['twitter:card' => 'summary']), false))->toBeNull();
});

test('Suppressed when empty', function () {
    $r = new DetectMissingTwitterCard();
    expect($r->evaluate(new TwitterCardCheckInput(['twitter:card' => '']), true))->toBeNull();
});

<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\Rules\DetectConflictingTwitterValues;
use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;

test('No conflict with single value', function () {
    expect((new DetectConflictingTwitterValues())->evaluate(new TwitterCardCheckInput(['twitter:card' => 'summary'])))->toBe([]);
});

test('No conflict with identical duplicates', function () {
    expect((new DetectConflictingTwitterValues())->evaluate(new TwitterCardCheckInput(['twitter:card' => ['summary', 'summary']])))->toBe([]);
});

test('Conflict with different values', function () {
    $s = (new DetectConflictingTwitterValues())->evaluate(new TwitterCardCheckInput(['twitter:card' => ['summary', 'player']]));
    expect($s)->toHaveCount(1);
    expect($s[0]->message)->toContain('Conflicting');
});

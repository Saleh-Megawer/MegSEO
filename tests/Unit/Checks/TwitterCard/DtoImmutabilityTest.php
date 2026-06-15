<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;
use MegSEO\Checks\TwitterCard\DTO\TwitterCardPropertyReport;
use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckMetadata;

test('TwitterCardCheckInput stores and retrieves', function () {
    $i = new TwitterCardCheckInput(['twitter:card' => 'summary', 'twitter:title' => 'Hi']);
    expect($i->has('twitter:card'))->toBeTrue();
    expect($i->has('twitter:image'))->toBeFalse();
    expect($i->get('twitter:title'))->toBe('Hi');
});

test('TwitterCardCheckInput detects empty', function () {
    $i = new TwitterCardCheckInput(['twitter:card' => '', 'twitter:title' => 'Hi']);
    expect($i->isEmpty('twitter:card'))->toBeTrue();
    expect($i->isEmpty('twitter:title'))->toBeFalse();
});

test('TwitterCardPropertyReport stores status', function () {
    $r = new TwitterCardPropertyReport('twitter:card', 'missing');
    expect($r->property)->toBe('twitter:card');
    expect($r->status)->toBe('missing');
});

test('TwitterCardCheckMetadata stores flags', function () {
    $m = new TwitterCardCheckMetadata('seo.twitter_card', twitterCardProvided: true, twitterImageProvided: false);
    expect($m->checkIdentifier)->toBe('seo.twitter_card');
    expect($m->twitterCardProvided)->toBeTrue();
    expect($m->twitterImageProvided)->toBeFalse();
});

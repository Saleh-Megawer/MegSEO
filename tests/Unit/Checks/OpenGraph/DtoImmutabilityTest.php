<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;
use MegSEO\Checks\OpenGraph\DTO\OpenGraphPropertyReport;
use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckMetadata;

test('OpenGraphCheckInput stores and retrieves properties', function () {
    $i = new OpenGraphCheckInput(['og:title' => 'Hello', 'og:description' => 'Desc']);
    expect($i->has('og:title'))->toBeTrue();
    expect($i->has('og:image'))->toBeFalse();
    expect($i->get('og:title'))->toBe('Hello');
});

test('OpenGraphCheckInput detects empty values', function () {
    $i = new OpenGraphCheckInput(['og:title' => '', 'og:description' => 'Valid']);
    expect($i->isEmpty('og:title'))->toBeTrue();
    expect($i->isEmpty('og:description'))->toBeFalse();
    expect($i->isEmpty('og:image'))->toBeFalse(); // not present = not empty
});

test('OpenGraphCheckInput handles array values', function () {
    $i = new OpenGraphCheckInput(['og:image' => ['img1.jpg', 'img2.jpg']]);
    expect($i->get('og:image'))->toBe('img1.jpg'); // first value
    expect($i->getArray('og:image'))->toBe(['img1.jpg', 'img2.jpg']);
});

test('OpenGraphPropertyReport stores status', function () {
    $r = new OpenGraphPropertyReport('og:title', 'missing', null, 'Not found');
    expect($r->property)->toBe('og:title');
    expect($r->status)->toBe('missing');
});

test('OpenGraphCheckMetadata stores flags', function () {
    $m = new OpenGraphCheckMetadata('seo.open_graph', ogTitleProvided: true, ogImageProvided: false);
    expect($m->checkIdentifier)->toBe('seo.open_graph');
    expect($m->ogTitleProvided)->toBeTrue();
    expect($m->ogImageProvided)->toBeFalse();
});

<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\Rules\EvaluateTwitterCardType;

test('Valid card types return null', function () {
    $r = new EvaluateTwitterCardType();
    foreach (['summary', 'summary_large_image', 'app', 'player'] as $t) {
        expect($r->evaluate($t, false))->toBeNull();
    }
});

test('Invalid card type returns warning', function () {
    $r = new EvaluateTwitterCardType();
    $w = $r->evaluate('photo', false);
    expect($w)->not->toBeNull();
    expect($w->message)->toBe('Invalid twitter:card type');
});

test('Empty suppressed — no invalid type finding', function () {
    $r = new EvaluateTwitterCardType();
    expect($r->evaluate('', true))->toBeNull();
    expect($r->evaluate(null, true))->toBeNull();
});

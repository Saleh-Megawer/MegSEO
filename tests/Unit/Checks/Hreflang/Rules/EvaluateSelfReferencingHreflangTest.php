<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\Rules\EvaluateSelfReferencingHreflang;
use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;

test('Passes when page-language entry matches page URL', function () {
    $r = new EvaluateSelfReferencingHreflang();
    $i = new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com/en'], ['hreflang'=>'fr','href'=>'https://x.com/fr']]);
    expect($r->evaluate($i, 'https://x.com/en', 'en'))->toBe([]);
});

test('Suggests when page-language entry does not match', function () {
    $r = new EvaluateSelfReferencingHreflang();
    $i = new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com/home']]);
    $s = $r->evaluate($i, 'https://x.com/en', 'en');
    expect($s)->toHaveCount(1);
    expect($s[0]->message)->toContain('self-reference');
});

test('Skips when page_url missing', function () {
    $r = new EvaluateSelfReferencingHreflang();
    $i = new HreflangCheckInput([['hreflang'=>'en','href'=>'https://x.com/en']]);
    expect($r->evaluate($i, null, 'en'))->toBe([]);
    expect($r->evaluate($i, '', 'en'))->toBe([]);
});

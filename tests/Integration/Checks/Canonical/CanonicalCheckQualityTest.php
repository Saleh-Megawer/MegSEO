<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\CanonicalCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Relative canonical produces warning', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(subject: '/relative-path'));
    expect($r->warnings())->toHaveCount(1);
    expect($r->warnings()[0]->message)->toBe('Canonical URL is relative');
});

test('Cross-domain canonical produces suggestion', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(
        subject: 'https://other.com/page',
        attributes: ['page_url' => 'https://example.com/page'],
    ));
    expect($r->suggestions())->toHaveCount(1);
    expect($r->suggestions()[0]->message)->toContain('different domain');
});

test('Non-self-referencing canonical produces suggestion', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(
        subject: 'https://example.com/canonical',
        attributes: ['page_url' => 'https://example.com/page'],
    ));
    expect($r->suggestions())->toHaveCount(1);
    expect($r->suggestions()[0]->message)->toContain('self-reference');
});

test('Self-referencing canonical passes without suggestions', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(
        subject: 'https://example.com/page',
        attributes: ['page_url' => 'https://example.com/page'],
    ));
    expect($r->suggestions())->toBe([]);
    expect($r->warnings())->toBe([]);
});

test('Missing page_url degrades gracefully', function () {
    $e = Engine::make();
    $e->register(new CanonicalCheck());
    $r = $e->analyze(new AnalysisContext(subject: 'https://other.com/page'));
    expect($r->failures)->toBe([]);
    expect($r->warnings())->toBe([]);
    expect($r->suggestions())->toBe([]);
});

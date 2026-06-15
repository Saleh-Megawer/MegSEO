<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\OpenGraphCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Arabic OG values pass', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['og:title' => 'عنوان الصفحة', 'og:description' => 'وصف الصفحة', 'og:image' => 'https://example.com/صورة.jpg']));
    expect($r->issues())->toBe([]);
});

test('Arabic OG with keyword scenario deterministic', function () {
    $e = Engine::make();
    $e->register(new OpenGraphCheck());
    $c = new AnalysisContext(subject: ['og:title' => 'مرحباً', 'og:description' => 'وصف', 'og:image' => 'https://x.com/img.jpg']);
    expect($e->analyze($c)->toArray())->toBe($e->analyze($c)->toArray());
});

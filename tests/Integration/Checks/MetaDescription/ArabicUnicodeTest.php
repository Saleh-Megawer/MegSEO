<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Arabic description with valid length passes', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = 'هذا هو وصف تجريبي طويل بما يكفي لاجتياز جميع اختبارات الطول والجودة في محرك MegSEO للتحليل والتدقيق';
    $result = $engine->analyze(new AnalysisContext(subject: $desc));
    expect($result->issues())->toBe([]);
    expect($result->warnings())->toBe([]);
});

test('Arabic short description produces warning', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $result = $engine->analyze(new AnalysisContext(subject: 'وصف قصير'));
    expect($result->warnings())->toHaveCount(1);
});

test('Arabic keyword match', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = 'هذا هو وصف تجريبي طويل بما يكفي يذكر منصة MegSEO لاجتياز جميع الاختبارات';
    $c = new AnalysisContext(subject: $desc, attributes: ['focus_keyword' => 'MegSEO']);
    expect($engine->analyze($c)->suggestions())->toBe([]);
});

test('Unicode special chars handled', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = 'Café — A description with the best coffee ☕ and pastries™. '.str_repeat('Enough characters to pass the length threshold check for meta descriptions. ', 2);
    $result = $engine->analyze(new AnalysisContext(subject: $desc));
    expect($result->issues())->toBe([]);
});

test('Arabic duplicate detected', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = 'هذا هو وصف تجريبي طويل بما يكفي لاجتياز جميع اختبارات الطول والجودة في محرك MegSEO للتحليل والتدقيق';
    $c = new AnalysisContext(subject: $desc, attributes: ['duplicate_support_data' => [['title' => $desc, 'reference' => '/home']]]);
    $result = $engine->analyze($c);
    expect($result->suggestions())->toHaveCount(1);
    expect($result->suggestions()[0]->message)->toContain('Duplicate');
});

test('Arabic deterministic repeated runs', function () {
    $engine = Engine::make();
    $engine->register(new MetaDescriptionCheck());
    $desc = 'هذا هو وصف تجريبي طويل بما يكفي لاجتياز جميع اختبارات الطول والجودة في محرك MegSEO للتحليل والتدقيق';
    $c = new AnalysisContext(subject: $desc, attributes: ['focus_keyword' => 'MegSEO']);
    expect($engine->analyze($c)->toArray())->toBe($engine->analyze($c)->toArray());
});

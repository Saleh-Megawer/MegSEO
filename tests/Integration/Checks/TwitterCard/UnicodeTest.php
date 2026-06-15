<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\TwitterCardCheck;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

test('Arabic values pass', function () {
    $e = Engine::make(); $e->register(new TwitterCardCheck());
    $r = $e->analyze(new AnalysisContext(subject: ['twitter:card'=>'summary','twitter:title'=>'عنوان','twitter:description'=>'وصف','twitter:image'=>'https://example.com/صورة.jpg']));
    expect($r->issues())->toBe([]);
});

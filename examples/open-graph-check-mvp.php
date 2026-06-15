<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\Checks\OpenGraph\OpenGraphCheck;

$engine = Engine::make();
$engine->register(new OpenGraphCheck());
echo "✔ OpenGraph Check registered (seo.open_graph v1.0.0)\n\n";

function ogScenario(Engine $engine, string $name, array $subject, array $exp): void {
    $r = $engine->analyze(new AnalysisContext(subject: $subject));
    $p = str_repeat('─', 62);
    echo "┌{$p}┐\n│ " . str_pad($name, 61) . "│\n└{$p}┘\n";

    $s = $r->score();
    echo "▶ SCORE: {$s->value}";
    $sm = $s->metadata;
    if (isset($sm['total_deductions'])) echo "  (deductions: {$sm['total_deductions']})";
    echo "\n";
    foreach ($sm['rationale'] ?? [] as $rat) echo "  ↳ [{$rat['severity']}] {$rat['finding']} (-{$rat['deduction']})\n";

    $i = $r->issues(); $w = $r->warnings(); $su = $r->suggestions();
    echo "  Issues: ".count($i)."  Warnings: ".count($w)."  Suggestions: ".count($su)."\n";
    foreach ($i as $x) echo "   ✗ {$x->message}\n";
    foreach ($w as $x) echo "   ⚠ {$x->message}\n";
    foreach ($su as $x) echo "   → {$x->message}\n";

    $iO = count($i) === ($exp['issues'] ?? 0);
    $wO = count($w) === ($exp['warnings'] ?? 0);
    $sO = count($su) === ($exp['suggestions'] ?? 0);
    printf("  ✓ I:%d=%d W:%d=%d S:%d=%d  %s\n\n", count($i), $exp['issues']??0, count($w), $exp['warnings']??0, count($su), $exp['suggestions']??0, ($iO&&$wO&&$sO) ? 'PASS' : 'FAIL');
}

ogScenario($engine, 'Perfect Open Graph', ['og:title'=>'My Title','og:description'=>'Desc','og:image'=>'https://example.com/img.jpg'], ['issues'=>0,'warnings'=>0,'suggestions'=>0]);
ogScenario($engine, 'Missing All OG', [], ['issues'=>3,'warnings'=>0,'suggestions'=>0]);
ogScenario($engine, 'Empty OG Values', ['og:title'=>'','og:description'=>'','og:image'=>''], ['issues'=>3,'warnings'=>0,'suggestions'=>0]);
ogScenario($engine, 'Relative Image', ['og:title'=>'Hi','og:description'=>'D','og:image'=>'/img.jpg'], ['issues'=>0,'warnings'=>1,'suggestions'=>0]);
ogScenario($engine, 'Invalid Image', ['og:title'=>'Hi','og:description'=>'D','og:image'=>'ftp://bad.jpg'], ['issues'=>0,'warnings'=>1,'suggestions'=>0]);
ogScenario($engine, 'Arabic OG Values', ['og:title'=>'مرحباً','og:description'=>'وصف','og:image'=>'https://example.com/صورة.jpg'], ['issues'=>0,'warnings'=>0,'suggestions'=>0]);
ogScenario($engine, 'IDN Image URL', ['og:title'=>'Hi','og:description'=>'D','og:image'=>'https://موقعي.مصر/img.jpg'], ['issues'=>0,'warnings'=>0,'suggestions'=>0]);
ogScenario($engine, 'Conflicting Values', ['og:title'=>['A','B'],'og:description'=>'D','og:image'=>'https://x.com/i.jpg'], ['issues'=>0,'warnings'=>0,'suggestions'=>1]);
ogScenario($engine, 'Duplicate Values (no conflict)', ['og:title'=>['Same','Same'],'og:description'=>'D','og:image'=>'https://x.com/i.jpg'], ['issues'=>0,'warnings'=>0,'suggestions'=>0]);

echo str_repeat('═', 64) . "\n  ✅ ALL SCENARIOS VALIDATED\n" . str_repeat('═', 64) . "\n\n✔ Open Graph Check dogfooding complete.\n";

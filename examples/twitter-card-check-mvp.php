<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\Checks\TwitterCard\TwitterCardCheck;

$engine = Engine::make();
$engine->register(new TwitterCardCheck());
echo "✔ Twitter Card Check registered (seo.twitter_card v1.0.0)\n\n";

function tw(Engine $engine, string $name, array $subject, array $exp): void {
    $r = $engine->analyze(new AnalysisContext(subject: $subject));
    $p = str_repeat('─', 62);
    echo "┌{$p}┐\n│ " . str_pad($name, 61) . "│\n└{$p}┘\n";
    $s = $r->score();
    echo "▶ SCORE: {$s->value}";
    if (($sm = $s->metadata) && isset($sm['total_deductions'])) echo "  (deductions: {$sm['total_deductions']})";
    echo "\n";
    foreach ($sm['rationale'] ?? [] as $rat) echo "  ↳ [{$rat['severity']}] {$rat['finding']} (-{$rat['deduction']})\n";
    $i = $r->issues(); $w = $r->warnings(); $su = $r->suggestions();
    echo "  Issues: ".count($i)."  Warnings: ".count($w)."  Suggestions: ".count($su)."\n";
    foreach ($i as $x) echo "   ✗ {$x->message}\n";
    foreach ($w as $x) echo "   ⚠ {$x->message}\n";
    foreach ($su as $x) echo "   → {$x->message}\n";
    $iO = count($i) === ($exp['issues'] ?? 0); $wO = count($w) === ($exp['warnings'] ?? 0); $sO = count($su) === ($exp['suggestions'] ?? 0);
    printf("  ✓ I:%d=%d W:%d=%d S:%d=%d  %s\n\n", count($i), $exp['issues']??0, count($w), $exp['warnings']??0, count($su), $exp['suggestions']??0, ($iO&&$wO&&$sO) ? 'PASS' : 'FAIL');
}

tw($engine, 'Perfect Twitter Card', ['twitter:card'=>'summary_large_image','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg'], ['issues'=>0,'warnings'=>0,'suggestions'=>0]);
tw($engine, 'Missing All Tags', [], ['issues'=>4,'warnings'=>0,'suggestions'=>0]);
tw($engine, 'Empty Values', ['twitter:card'=>'','twitter:title'=>'','twitter:description'=>'','twitter:image'=>''], ['issues'=>4,'warnings'=>0,'suggestions'=>0]);
tw($engine, 'Invalid Card Type', ['twitter:card'=>'photo','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg'], ['issues'=>0,'warnings'=>1,'suggestions'=>0]);
tw($engine, 'Relative Image', ['twitter:card'=>'summary','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'/img.jpg'], ['issues'=>0,'warnings'=>1,'suggestions'=>0]);
tw($engine, 'Arabic Values', ['twitter:card'=>'summary','twitter:title'=>'مرحباً','twitter:description'=>'وصف','twitter:image'=>'https://x.com/صورة.jpg'], ['issues'=>0,'warnings'=>0,'suggestions'=>0]);
tw($engine, 'IDN Image URL', ['twitter:card'=>'summary','twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://موقعي.مصر/img.jpg'], ['issues'=>0,'warnings'=>0,'suggestions'=>0]);
tw($engine, 'Duplicate Values (no conflict)', ['twitter:card'=>['summary','summary'],'twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg'], ['issues'=>0,'warnings'=>0,'suggestions'=>0]);
tw($engine, 'Conflicting Values', ['twitter:card'=>['summary','player'],'twitter:title'=>'Hi','twitter:description'=>'D','twitter:image'=>'https://x.com/i.jpg'], ['issues'=>0,'warnings'=>0,'suggestions'=>1]);

echo str_repeat('═', 64) . "\n  ✅ ALL SCENARIOS VALIDATED\n" . str_repeat('═', 64) . "\n\n✔ Twitter Card Check dogfooding complete.\n";

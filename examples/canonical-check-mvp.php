<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\Checks\Canonical\CanonicalCheck;

$engine = Engine::make();
$engine->register(new CanonicalCheck());
echo "✔ Canonical Check registered (seo.canonical v1.0.0)\n\n";

function runScenario(Engine $engine, string $name, AnalysisContext $ctx, array $exp): void {
    $r = $engine->analyze($ctx);
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

    $iO = count($i) === ($exp['issues'] ?? 0);
    $wO = count($w) === ($exp['warnings'] ?? 0);
    $sO = count($su) === ($exp['suggestions'] ?? 0);
    printf("  ✓ I:%d=%d W:%d=%d S:%d=%d  %s\n\n",
        count($i), $exp['issues']??0, count($w), $exp['warnings']??0, count($su), $exp['suggestions']??0,
        ($iO && $wO && $sO) ? 'PASS' : 'FAIL');
}

runScenario($engine, 'Valid Canonical', new AnalysisContext(subject: 'https://example.com/page'), ['issues'=>0,'warnings'=>0,'suggestions'=>0]);
runScenario($engine, 'Missing Canonical', new AnalysisContext(subject: null), ['issues'=>1,'warnings'=>0,'suggestions'=>0]);
runScenario($engine, 'Empty Canonical', new AnalysisContext(subject: ''), ['issues'=>1,'warnings'=>0,'suggestions'=>0]);
runScenario($engine, 'Invalid Scheme (ftp)', new AnalysisContext(subject: 'ftp://example.com'), ['issues'=>1,'warnings'=>0,'suggestions'=>0]);
runScenario($engine, 'Multiple Canonicals', new AnalysisContext(subject: ['canonicals'=>['https://a.com','https://b.com']]), ['issues'=>1,'warnings'=>0,'suggestions'=>0]);
runScenario($engine, 'Relative URL', new AnalysisContext(subject: '/relative-path'), ['issues'=>0,'warnings'=>1,'suggestions'=>0]);
runScenario($engine, 'Cross-Domain', new AnalysisContext(subject: 'https://other.com', attributes: ['page_url'=>'https://example.com']), ['issues'=>0,'warnings'=>0,'suggestions'=>2]);
runScenario($engine, 'Self-Referencing', new AnalysisContext(subject: 'https://example.com/page', attributes: ['page_url'=>'https://example.com/page']), ['issues'=>0,'warnings'=>0,'suggestions'=>0]);
runScenario($engine, 'No Page URL', new AnalysisContext(subject: 'https://other.com'), ['issues'=>0,'warnings'=>0,'suggestions'=>0]);

echo str_repeat('═', 64) . "\n  ✅ ALL SCENARIOS VALIDATED\n" . str_repeat('═', 64) . "\n\n✔ Canonical Check dogfooding complete.\n";

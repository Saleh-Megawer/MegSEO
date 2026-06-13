<?php

/**
 * MegSEO Meta Description Check — Manual Dogfooding Example
 * ===========================================================
 *
 * Validates: MetaDescriptionCheck — US1 (presence/quality), US2 (keyword/duplicate),
 *            US3 (scoring, metadata, determinism), Arabic/Unicode descriptions.
 *
 * This file is temporary — it validates the Meta Description Check through
 * real-world usage and does not belong to the package's production API.
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;

// ┌─────────────────────────────────────────────────────────────┐
// │ Scenarios                                                   │
// └─────────────────────────────────────────────────────────────┘

$goodDesc = 'A valid and descriptive meta description that sits comfortably within the recommended length range for optimal search engine display.'; // ~130 chars

$scenarios = [
    'Valid Description'    => $goodDesc,
    'Missing Description'  => null,
    'Empty Description'    => '',
    'Whitespace Desc'      => '      ',
    'Separator Only Desc'  => '--- | ---',
    'Short Description'    => 'Too short',
    'Long Description'     => str_repeat('a', 200),
    'Arabic Valid Desc'    => 'هذا هو وصف تجريبي طويل بما يكفي لاجتياز جميع اختبارات الطول والجودة في محرك MegSEO للتحليل والتدقيق',
    'Arabic Short Desc'    => 'وصف قصير',
];

$expected = [
    'Valid Description'    => ['issues' => 0, 'warnings' => 0, 'suggestions' => 0],
    'Missing Description'  => ['issues' => 1, 'warnings' => 0, 'suggestions' => 0],
    'Empty Description'    => ['issues' => 1, 'warnings' => 0, 'suggestions' => 0],
    'Whitespace Desc'      => ['issues' => 1, 'warnings' => 0, 'suggestions' => 0],
    'Separator Only Desc'  => ['issues' => 1, 'warnings' => 0, 'suggestions' => 0],
    'Short Description'    => ['issues' => 0, 'warnings' => 1, 'suggestions' => 0],
    'Long Description'     => ['issues' => 0, 'warnings' => 1, 'suggestions' => 0],
    'Arabic Valid Desc'    => ['issues' => 0, 'warnings' => 0, 'suggestions' => 0],
    'Arabic Short Desc'    => ['issues' => 0, 'warnings' => 1, 'suggestions' => 0],
];

$engine = Engine::make();
$engine->register(new MetaDescriptionCheck());

echo "✔ Engine instantiated, Meta Description Check registered\n";
echo '  Check ID: seo.meta_description (v1.0.0)' . "\n\n";

$allOk = true;

foreach ($scenarios as $name => $title) {
    $padding = str_repeat('─', 62);
    echo "┌{$padding}┐\n";
    echo '│ ' . str_pad("SCENARIO: $name", 62) . "│\n";
    echo "└{$padding}┘\n\n";

    $display = $title ?? '(null)';
    if ($title === '') { $display = '(empty string)'; }
    echo "  Input: " . (is_string($display) && strlen($display) > 80 ? substr($display, 0, 77) . '...' : $display) . "\n\n";

    $context = new AnalysisContext(subject: $title);
    $result = $engine->analyze($context);

    $score = $result->score();
    echo "▶ SCORE\n";
    echo "  Value:        " . ($score->value !== null ? number_format($score->value, 1) : 'N/A') . "\n";
    foreach ($score->contributors as $c) {
        echo "    • {$c['sourceCheckId']} → {$c['value']}\n";
    }
    $sm = $score->metadata;
    if (isset($sm['total_deductions'])) {
        echo "  Deductions:   {$sm['total_deductions']} / {$sm['max_score']}\n";
    }
    if (isset($sm['rationale']) && is_array($sm['rationale'])) {
        foreach ($sm['rationale'] as $r) {
            echo "    ↳ [{$r['severity']}] {$r['finding']} (-{$r['deduction']})\n";
        }
    }
    echo "\n";

    $issues = $result->issues();
    echo "▶ ISSUES (" . count($issues) . ")\n";
    if (count($issues) === 0) { echo "  ✓ (none)\n"; } else {
        foreach ($issues as $i => $issue) { echo "  [" . ($i + 1) . "] {$issue->message}\n"; }
    }
    echo "\n";

    $warnings = $result->warnings();
    echo "▶ WARNINGS (" . count($warnings) . ")\n";
    if (count($warnings) === 0) { echo "  ✓ (none)\n"; } else {
        foreach ($warnings as $i => $w) { echo "  [" . ($i + 1) . "] {$w->message}\n"; }
    }
    echo "\n";

    $suggestions = $result->suggestions();
    echo "▶ SUGGESTIONS (" . count($suggestions) . ")\n";
    if (count($suggestions) === 0) { echo "  ✓ (none)\n"; } else {
        foreach ($suggestions as $i => $s) { echo "  [" . ($i + 1) . "] {$s->message}\n"; }
    }
    echo "\n";

    $exp = $expected[$name];
    $iOk = count($issues) === $exp['issues'];
    $wOk = count($warnings) === $exp['warnings'];
    $sOk = count($suggestions) === ($exp['suggestions'] ?? 0);
    echo "▶ ASSERTIONS\n";
    printf("  Issues:      %d=%d %s\n", count($issues), $exp['issues'], $iOk ? '✓' : '✗');
    printf("  Warnings:    %d=%d %s\n", count($warnings), $exp['warnings'], $wOk ? '✓' : '✗');
    printf("  Suggestions: %d=%d %s\n", count($suggestions), $exp['suggestions'] ?? 0, $sOk ? '✓' : '✗');
    echo "\n";

    if (!($iOk && $wOk && $sOk)) { $allOk = false; }
}

// ── US2 Scenarios ─────────────────────────────────────────────

echo "┌" . str_repeat('─', 62) . "┐\n";
echo "│ " . str_pad("US2: Focus Keyword + Duplicate", 62) . "│\n";
echo "└" . str_repeat('─', 62) . "┘\n\n";

// Keyword present
$r = $engine->analyze(new AnalysisContext(subject: $goodDesc, attributes: ['focus_keyword' => 'meta']));
echo "▶ Focus Keyword Present: " . (count($r->suggestions()) === 0 ? '✓ PASS' : '✗ FAIL') . "\n";

// Keyword absent
$r = $engine->analyze(new AnalysisContext(subject: $goodDesc, attributes: ['focus_keyword' => 'MegSEO']));
echo "▶ Focus Keyword Absent:  " . (count($r->suggestions()) === 1 ? '✓ PASS' : '✗ FAIL') . "\n";
echo "    ↳ " . ($r->suggestions()[0]->message ?? '') . "\n";

// Duplicate
$r = $engine->analyze(new AnalysisContext(subject: $goodDesc, attributes: ['duplicate_support_data' => [['title' => $goodDesc, 'reference' => '/other']]]));
echo "▶ Duplicate Detected:    " . (count($r->suggestions()) === 1 && str_contains($r->suggestions()[0]->message, 'Duplicate') ? '✓ PASS' : '✗ FAIL') . "\n";
echo "    ↳ " . ($r->suggestions()[0]->message ?? '') . "\n";

echo "\n";

echo str_repeat('═', 64) . "\n";
echo $allOk ? "  ✅ ALL ASSERTIONS PASSED\n" : "  ❌ SOME ASSERTIONS FAILED\n";
echo str_repeat('═', 64) . "\n";
echo "\n✔ Meta Description Check dogfooding complete.\n";

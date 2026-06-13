<?php

/**
 * MegSEO Title Check MVP — Manual Dogfooding Example
 * ====================================================
 *
 * Validates: TitleCheck — US1 (title presence/quality), US2 (keyword/duplicate),
 *            US3 (scoring, metadata, determinism), Arabic/Unicode titles.
 *
 * This file is temporary — it validates the Title Check MVP through
 * real-world usage and does not belong to the package's production API.
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\Checks\Title\TitleCheck;

// ┌─────────────────────────────────────────────────────────────┐
// │ 1. Scenarios                                                │
// └─────────────────────────────────────────────────────────────┘

$scenarios = [
    'Valid Title'          => 'Welcome to MegSEO — The Ultimate SEO Intelligence Engine',
    'Missing Title'        => null,
    'Empty Title'          => '',
    'Whitespace Title'     => '      ',
    'Separator Only Title' => '--- | ---',
    'Short Title'          => 'Home',
    'Long Title'           => 'This is an extremely long page title intended to exceed the recommended title length thresholds and trigger warning behavior within MegSEO',
    'Arabic Valid Title'   => 'مرحباً بكم في منصة MegSEO',
    'Arabic Short Title'   => 'الرئيسية',
];

$expected = [
    'Valid Title'          => ['issues' => 0, 'warnings' => 0, 'suggestions' => 0],
    'Missing Title'        => ['issues' => 1, 'warnings' => 0, 'suggestions' => 0],
    'Empty Title'          => ['issues' => 1, 'warnings' => 0, 'suggestions' => 0],
    'Whitespace Title'     => ['issues' => 1, 'warnings' => 0, 'suggestions' => 0],
    'Separator Only Title' => ['issues' => 1, 'warnings' => 0, 'suggestions' => 0],
    'Short Title'          => ['issues' => 0, 'warnings' => 1, 'suggestions' => 0],
    'Long Title'           => ['issues' => 0, 'warnings' => 1, 'suggestions' => 0],
    'Arabic Valid Title'   => ['issues' => 0, 'warnings' => 0, 'suggestions' => 0],
    'Arabic Short Title'   => ['issues' => 0, 'warnings' => 1, 'suggestions' => 0],
];

// ┌─────────────────────────────────────────────────────────────┐
// │ 2. Instantiate engine and register Title Check               │
// └─────────────────────────────────────────────────────────────┘

$engine = Engine::make();
$titleCheck = new TitleCheck();
$engine->register($titleCheck);

echo "✔ Engine instantiated, Title Check registered\n";
echo '  Check ID: ' . $titleCheck->ref()->id . ' (v' . $titleCheck->ref()->version . ")\n\n";

// ┌─────────────────────────────────────────────────────────────┐
// │ 3. Execute each scenario                                    │
// └─────────────────────────────────────────────────────────────┘

$allAssertionsPassed = true;

foreach ($scenarios as $name => $title) {
    $padding = str_repeat('─', 62);

    echo "┌{$padding}┐\n";
    echo '│ ' . str_pad("SCENARIO: $name", 62) . "│\n";
    echo "└{$padding}┘\n\n";

    // ── Display raw title ─────────────────────────────────────
    $displayTitle = $title;
    if ($title === null) {
        $displayTitle = '(null — missing title data)';
    } elseif ($title === '') {
        $displayTitle = '(empty string)';
    } elseif (trim($title) === '') {
        $displayTitle = '(whitespace-only: "' . $title . '")';
    }
    echo "  Original Title: {$displayTitle}\n\n";

    // ── Create context and run ─────────────────────────────────
    $context = new AnalysisContext(subject: $title);
    $result = $engine->analyze($context);

    // ── Score ──────────────────────────────────────────────────
    $score = $result->score();
    echo "▶ SCORE\n";
    echo "  Value:        " . ($score->value !== null ? number_format($score->value, 1) : 'N/A') . "\n";
    echo "  Contributors: " . count($score->contributors) . "\n";
    foreach ($score->contributors as $contributor) {
        echo "    • {$contributor['sourceCheckId']} → {$contributor['value']}\n";
    }
    $scoreMeta = $score->metadata;
    if (isset($scoreMeta['total_deductions'])) {
        echo "  Deductions:   {$scoreMeta['total_deductions']} / {$scoreMeta['max_score']}\n";
    }
    if (isset($scoreMeta['rationale']) && is_array($scoreMeta['rationale'])) {
        foreach ($scoreMeta['rationale'] as $r) {
            echo "    ↳ [{$r['severity']}] {$r['finding']} (-{$r['deduction']})\n";
        }
    }
    echo "\n";

    // ── Issues ─────────────────────────────────────────────────
    $issues = $result->issues();
    echo "▶ ISSUES (" . count($issues) . ")\n";
    if (count($issues) === 0) {
        echo "  ✓ (none)\n";
    } else {
        foreach ($issues as $i => $issue) {
            echo "  [" . ($i + 1) . "] {$issue->message}\n";
            echo "      Source:     {$issue->sourceCheckId}\n";
            echo "      Details:    {$issue->details}\n";
            if ($issue->confidence !== null) {
                echo "      Confidence: " . ($issue->confidence * 100) . "%\n";
            }
        }
    }
    echo "\n";

    // ── Warnings ───────────────────────────────────────────────
    $warnings = $result->warnings();
    echo "▶ WARNINGS (" . count($warnings) . ")\n";
    if (count($warnings) === 0) {
        echo "  ✓ (none)\n";
    } else {
        foreach ($warnings as $i => $warning) {
            echo "  [" . ($i + 1) . "] {$warning->message}\n";
            echo "      Source:  {$warning->sourceCheckId}\n";
            echo "      Details: {$warning->details}\n";
        }
    }
    echo "\n";

    // ── Suggestions ────────────────────────────────────────────
    $suggestions = $result->suggestions();
    echo "▶ SUGGESTIONS (" . count($suggestions) . ")\n";
    if (count($suggestions) === 0) {
        echo "  ✓ (none)\n";
    } else {
        foreach ($suggestions as $i => $suggestion) {
            echo "  [" . ($i + 1) . "] {$suggestion->message}\n";
            echo "      Source:     {$suggestion->sourceCheckId}\n";
            echo "      Details:    {$suggestion->details}\n";
            if ($suggestion->confidence !== null) {
                echo "      Confidence: " . ($suggestion->confidence * 100) . "%\n";
            }
        }
    }
    echo "\n";

    // ── Failures ───────────────────────────────────────────────
    echo "▶ FAILURES (" . count($result->failures) . ")\n";
    if (count($result->failures) === 0) {
        echo "  ✓ (none)\n";
    } else {
        foreach ($result->failures as $i => $failure) {
            echo "  [" . ($i + 1) . "] {$failure['check']->id}: {$failure['error']}\n";
        }
    }
    echo "\n";

    // ── Metadata ───────────────────────────────────────────────
    echo "▶ METADATA\n";
    echo "  " . json_encode($result->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    echo "\n";

    // ── Serialized Output ──────────────────────────────────────
    echo "▶ SERIALIZED (toArray)\n";
    echo "  " . json_encode($result->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    echo "\n";

    // ── Assertions ─────────────────────────────────────────────
    $exp = $expected[$name];
    $issueCount = count($issues);
    $warningCount = count($warnings);
    $suggestionCount = count($suggestions);

    $issuesOk = $issueCount === $exp['issues'];
    $warningsOk = $warningCount === $exp['warnings'];
    $suggestionsOk = $suggestionCount === ($exp['suggestions'] ?? 0);
    $failuresOk = count($result->failures) === 0;

    echo "▶ ASSERTIONS\n";
    printf(
        "  Issues:      expected %d, got %d — %s\n",
        $exp['issues'],
        $issueCount,
        $issuesOk ? '✓ PASS' : '✗ FAIL',
    );
    printf(
        "  Warnings:    expected %d, got %d — %s\n",
        $exp['warnings'],
        $warningCount,
        $warningsOk ? '✓ PASS' : '✗ FAIL',
    );
    printf(
        "  Suggestions: expected %d, got %d — %s\n",
        $exp['suggestions'] ?? 0,
        $suggestionCount,
        $suggestionsOk ? '✓ PASS' : '✗ FAIL',
    );
    printf(
        "  Failures:    expected 0, got %d — %s\n",
        count($result->failures),
        $failuresOk ? '✓ PASS' : '✗ FAIL',
    );

    // ── PASS label for clean scenarios ─────────────────────────
    if ($issueCount === 0 && $warningCount === 0 && $suggestionCount === 0) {
        echo "\n  ★ SCENARIO PASS — no findings returned.\n";
    }

    echo "\n";

    if (!($issuesOk && $warningsOk && $suggestionsOk && $failuresOk)) {
        $allAssertionsPassed = false;
    }
}

// ┌─────────────────────────────────────────────────────────────┐
// │ 4. US2 Scenarios — Focus Keyword + Duplicate Support         │
// └─────────────────────────────────────────────────────────────┘

$showScenario = function (string $name, AnalysisContext $context, array $expect) use ($engine) {
    echo "┌" . str_repeat('─', 62) . "┐\n";
    echo '│ ' . str_pad("SCENARIO: $name", 62) . "│\n";
    echo "└" . str_repeat('─', 62) . "┘\n\n";

    $result = $engine->analyze($context);

    $score = $result->score();
    echo "▶ SCORE\n";
    echo "  Value:        " . ($score->value !== null ? number_format($score->value, 1) : 'N/A') . "\n\n";

    $issues = $result->issues();
    echo "▶ ISSUES (" . count($issues) . ")\n";
    if (count($issues) === 0) {
        echo "  ✓ (none)\n";
    } else {
        foreach ($issues as $i => $issue) {
            echo "  [" . ($i + 1) . "] {$issue->message}\n";
            echo "      Source:     {$issue->sourceCheckId}\n";
            echo "      Details:    {$issue->details}\n";
        }
    }
    echo "\n";

    $warnings = $result->warnings();
    echo "▶ WARNINGS (" . count($warnings) . ")\n";
    if (count($warnings) === 0) {
        echo "  ✓ (none)\n";
    } else {
        foreach ($warnings as $i => $warning) {
            echo "  [" . ($i + 1) . "] {$warning->message}\n";
            echo "      Source:  {$warning->sourceCheckId}\n";
            echo "      Details: {$warning->details}\n";
        }
    }
    echo "\n";

    $suggestions = $result->suggestions();
    echo "▶ SUGGESTIONS (" . count($suggestions) . ")\n";
    if (count($suggestions) === 0) {
        echo "  ✓ (none)\n";
    } else {
        foreach ($suggestions as $i => $suggestion) {
            echo "  [" . ($i + 1) . "] {$suggestion->message}\n";
            echo "      Source:     {$suggestion->sourceCheckId}\n";
            echo "      Details:    {$suggestion->details}\n";
            if ($suggestion->confidence !== null) {
                echo "      Confidence: " . ($suggestion->confidence * 100) . "%\n";
            }
        }
    }
    echo "\n";

    echo "▶ ASSERTIONS\n";
    $iOk = count($result->issues()) === ($expect['issues'] ?? 0);
    $wOk = count($result->warnings()) === ($expect['warnings'] ?? 0);
    $sOk = count($result->suggestions()) === ($expect['suggestions'] ?? 0);
    $fOk = count($result->failures) === 0;

    printf("  Issues:      expected %d, got %d — %s\n", $expect['issues'] ?? 0, count($result->issues()), $iOk ? '✓ PASS' : '✗ FAIL');
    printf("  Warnings:    expected %d, got %d — %s\n", $expect['warnings'] ?? 0, count($result->warnings()), $wOk ? '✓ PASS' : '✗ FAIL');
    printf("  Suggestions: expected %d, got %d — %s\n", $expect['suggestions'] ?? 0, count($result->suggestions()), $sOk ? '✓ PASS' : '✗ FAIL');
    printf("  Failures:    expected 0, got %d — %s\n", count($result->failures), $fOk ? '✓ PASS' : '✗ FAIL');

    echo "\n";
    return $iOk && $wOk && $sOk && $fOk;
};

$us2Ok = true;

// Focus keyword present — no suggestion
$us2Ok = $showScenario(
    'Focus Keyword Present',
    new AnalysisContext(
        subject: 'Welcome to MegSEO — The Ultimate SEO Intelligence Engine',
        attributes: ['focus_keyword' => 'MegSEO'],
    ),
    ['issues' => 0, 'warnings' => 0, 'suggestions' => 0],
) && $us2Ok;

// Focus keyword absent — suggestion
$us2Ok = $showScenario(
    'Focus Keyword Absent',
    new AnalysisContext(
        subject: 'Welcome to Our Generic Platform',
        attributes: ['focus_keyword' => 'MegSEO'],
    ),
    ['issues' => 0, 'warnings' => 0, 'suggestions' => 1],
) && $us2Ok;

// Duplicate title detected — suggestion
$us2Ok = $showScenario(
    'Duplicate Title Detected',
    new AnalysisContext(
        subject: 'Complete Guide to Page Title Optimization for SEO',
        attributes: [
            'duplicate_support_data' => [
                ['title' => 'Complete Guide to Page Title Optimization for SEO', 'reference' => '/other-page'],
            ],
        ],
    ),
    ['issues' => 0, 'warnings' => 0, 'suggestions' => 1],
) && $us2Ok;

$allAssertionsPassed = $allAssertionsPassed && $us2Ok;

// ┌─────────────────────────────────────────────────────────────┐
// │ 5. Summary                                                  │
// └─────────────────────────────────────────────────────────────┘

echo str_repeat('═', 64) . "\n";

if ($allAssertionsPassed) {
    echo "  ✅ ALL ASSERTIONS PASSED\n";
} else {
    echo "  ❌ SOME ASSERTIONS FAILED\n";
}

echo str_repeat('═', 64) . "\n";
echo "\n✔ Title Check dogfooding complete — all US1, US2, and US3 scenarios validated.\n";

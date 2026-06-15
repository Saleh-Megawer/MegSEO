<?php

declare(strict_types=1);

use MegSEO\Checks\Canonical\Scoring\CanonicalScoreContributionBuilder;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\ScoreSummary;

test('Max score for clean canonical', function () {
    $b = new CanonicalScoreContributionBuilder();
    $s = $b->build([], [], []);
    expect($s)->toBeInstanceOf(ScoreSummary::class);
    expect($s->value)->toBe(100.0);
});

test('Missing canonical penalty', function () {
    $b = new CanonicalScoreContributionBuilder();
    $s = $b->build([new AnalysisIssue('Canonical tag is missing', '', 'seo.canonical')], [], []);
    expect($s->value)->toBe(60.0);
    expect($s->contributors[0]['value'])->toBe(-40.0);
});

test('Invalid canonical penalty', function () {
    $b = new CanonicalScoreContributionBuilder();
    $s = $b->build([new AnalysisIssue('Canonical URL is invalid', '', 'seo.canonical')], [], []);
    expect($s->value)->toBe(70.0);
});

test('Relative warning penalty', function () {
    $b = new CanonicalScoreContributionBuilder();
    $s = $b->build([], [new AnalysisWarning('Canonical URL is relative', '', 'seo.canonical')], []);
    expect($s->value)->toBe(85.0);
});

test('Cross-domain suggestion penalty', function () {
    $b = new CanonicalScoreContributionBuilder();
    $s = $b->build([], [], [new AnalysisSuggestion('Canonical URL points to a different domain', '', 'seo.canonical')]);
    expect($s->value)->toBe(90.0);
});

test('Accumulates deductions', function () {
    $b = new CanonicalScoreContributionBuilder();
    $s = $b->build(
        [new AnalysisIssue('Canonical tag is missing', '', 'seo.canonical')],
        [new AnalysisWarning('Canonical URL is relative', '', 'seo.canonical')],
        [new AnalysisSuggestion('Canonical URL does not self-reference the page', '', 'seo.canonical')],
    );
    expect($s->value)->toBe(40.0);
    expect($s->contributors)->toHaveCount(3);
});

test('Includes rationale metadata', function () {
    $b = new CanonicalScoreContributionBuilder();
    $s = $b->build([new AnalysisIssue('Canonical tag is missing', '', 'seo.canonical')], [], []);
    expect($s->metadata)->toHaveKey('rationale');
    expect($s->metadata)->toHaveKey('max_score');
    expect($s->metadata)->toHaveKey('total_deductions');
});

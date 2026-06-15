<?php

declare(strict_types=1);

use MegSEO\Checks\OpenGraph\Scoring\OpenGraphScoreContributionBuilder;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\ScoreSummary;

test('Clean OG gives max score', function () {
    $b = new OpenGraphScoreContributionBuilder();
    expect($b->build([], [], [])->value)->toBe(100.0);
});

test('Missing og:title penalty', function () {
    $b = new OpenGraphScoreContributionBuilder();
    $s = $b->build([new AnalysisIssue('og:title is missing', '', 'seo.open_graph')], [], []);
    expect($s->value)->toBe(75.0);
});

test('Missing og:image penalty', function () {
    $b = new OpenGraphScoreContributionBuilder();
    $s = $b->build([new AnalysisIssue('og:image is missing', '', 'seo.open_graph')], [], []);
    expect($s->value)->toBe(75.0);
});

test('Empty OG penalty', function () {
    $b = new OpenGraphScoreContributionBuilder();
    $s = $b->build([new AnalysisIssue('og:title is empty', '', 'seo.open_graph')], [], []);
    expect($s->value)->toBe(80.0);
});

test('Relative image penalty', function () {
    $b = new OpenGraphScoreContributionBuilder();
    $s = $b->build([], [new AnalysisWarning('og:image URL is relative', '', 'seo.open_graph')], []);
    expect($s->value)->toBe(90.0);
});

test('Conflict penalty', function () {
    $b = new OpenGraphScoreContributionBuilder();
    $s = $b->build([], [], [new AnalysisSuggestion('Conflicting og:title values detected', '', 'seo.open_graph')]);
    expect($s->value)->toBe(85.0);
});

test('Accumulates deductions', function () {
    $b = new OpenGraphScoreContributionBuilder();
    $s = $b->build(
        [new AnalysisIssue('og:title is missing', '', 'seo.open_graph'), new AnalysisIssue('og:title is empty', '', 'seo.open_graph')],
        [new AnalysisWarning('og:image URL is relative', '', 'seo.open_graph')],
        [new AnalysisSuggestion('Conflicting og:title values detected', '', 'seo.open_graph')],
    );
    expect($s->value)->toBe(30.0); // 100 - 25 - 20 - 10 - 15
    expect($s->contributors)->toHaveCount(4);
});

test('Rationale metadata present', function () {
    $b = new OpenGraphScoreContributionBuilder();
    $s = $b->build([new AnalysisIssue('og:title is missing', '', 'seo.open_graph')], [], []);
    expect($s->metadata)->toHaveKey('rationale');
    expect($s->metadata)->toHaveKey('max_score');
    expect($s->metadata)->toHaveKey('total_deductions');
});

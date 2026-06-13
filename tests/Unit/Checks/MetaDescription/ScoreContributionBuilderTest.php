<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Scoring\MetaDescriptionScoreContributionBuilder;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\ScoreSummary;

test('Returns max score for clean description', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $s = $b->build([], [], []);
    expect($s)->toBeInstanceOf(ScoreSummary::class);
    expect($s->value)->toBe(100.0);
    expect($s->contributors)->toBe([]);
});

test('Penalizes missing description', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $s = $b->build([new AnalysisIssue('Meta description is missing', '', 'seo.meta_description')], [], []);
    expect($s->value)->toBe(60.0);
    expect($s->contributors[0]['value'])->toBe(-40.0);
});

test('Penalizes empty description', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $s = $b->build([new AnalysisIssue('Meta description is empty', '', 'seo.meta_description')], [], []);
    expect($s->value)->toBe(65.0);
});

test('Penalizes short description', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $s = $b->build([], [new AnalysisWarning('Meta description is too short', '', 'seo.meta_description')], []);
    expect($s->value)->toBe(85.0);
});

test('Penalizes long description', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $s = $b->build([], [new AnalysisWarning('Meta description is too long', '', 'seo.meta_description')], []);
    expect($s->value)->toBe(90.0);
});

test('Penalizes missing keyword suggestion', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $s = $b->build([], [], [new AnalysisSuggestion('Focus keyword "seo" not found in meta description', '', 'seo.meta_description')]);
    expect($s->value)->toBe(95.0);
});

test('Penalizes duplicate suggestion', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $s = $b->build([], [], [new AnalysisSuggestion('Duplicate meta description detected', '', 'seo.meta_description')]);
    expect($s->value)->toBe(92.0);
});

test('Accumulates deductions', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $s = $b->build(
        [new AnalysisIssue('Meta description is missing', '', 'seo.meta_description')],
        [new AnalysisWarning('Meta description is too short', '', 'seo.meta_description')],
        [new AnalysisSuggestion('Focus keyword "x" not found in meta description', '', 'seo.meta_description')],
    );
    expect($s->value)->toBe(40.0);
    expect($s->contributors)->toHaveCount(3);
});

test('Never below zero', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $issues = [new AnalysisIssue('Meta description is missing', '', 'seo.meta_description'), new AnalysisIssue('Meta description is empty', '', 'seo.meta_description'), new AnalysisIssue('Title contains only punctuation', '', 'seo.meta_description')];
    $warnings = [new AnalysisWarning('Meta description is too short', '', 'seo.meta_description'), new AnalysisWarning('Meta description is too long', '', 'seo.meta_description')];
    $s = $b->build($issues, $warnings, []);
    expect($s->value)->toBe(0.0);
});

test('Includes rationale metadata', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $s = $b->build([new AnalysisIssue('Meta description is missing', '', 'seo.meta_description')], [], []);
    expect($s->metadata)->toHaveKey('rationale');
    expect($s->metadata)->toHaveKey('max_score');
    expect($s->metadata)->toHaveKey('total_deductions');
});

test('Deterministic', function () {
    $b = new MetaDescriptionScoreContributionBuilder();
    $i = [new AnalysisIssue('Meta description is missing', '', 'seo.meta_description')];
    $s1 = $b->build($i, [], []);
    $s2 = $b->build($i, [], []);
    expect($s1->value)->toBe($s2->value);
    expect($s1->contributors)->toBe($s2->contributors);
    expect($s1->metadata)->toBe($s2->metadata);
});

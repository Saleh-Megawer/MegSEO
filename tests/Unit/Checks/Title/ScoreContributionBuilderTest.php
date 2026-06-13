<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Scoring\TitleScoreContributionBuilder;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\ScoreSummary;

test('TitleScoreContributionBuilder returns max score for clean title', function () {
    $builder = new TitleScoreContributionBuilder();
    $score = $builder->build([], [], []);

    expect($score)->toBeInstanceOf(ScoreSummary::class);
    expect($score->value)->toBe(100.0);
    expect($score->contributors)->toBe([]);
});

test('TitleScoreContributionBuilder penalizes missing title', function () {
    $builder = new TitleScoreContributionBuilder();
    $issues = [
        new AnalysisIssue('Title is missing', 'No title data supplied.', sourceCheckId: 'seo.title'),
    ];
    $score = $builder->build($issues, [], []);

    expect($score->value)->toBe(60.0);
    expect($score->contributors)->toHaveCount(1);
    expect($score->contributors[0]['value'])->toBe(-40.0);
});

test('TitleScoreContributionBuilder penalizes empty title', function () {
    $builder = new TitleScoreContributionBuilder();
    $issues = [
        new AnalysisIssue('Title is empty', 'The title is empty.', sourceCheckId: 'seo.title'),
    ];
    $score = $builder->build($issues, [], []);

    expect($score->value)->toBe(65.0);
});

test('TitleScoreContributionBuilder penalizes separator-only title', function () {
    $builder = new TitleScoreContributionBuilder();
    $issues = [
        new AnalysisIssue(
            'Title contains only punctuation, separators, or whitespace',
            'No meaningful content.',
            sourceCheckId: 'seo.title',
        ),
    ];
    $score = $builder->build($issues, [], []);

    expect($score->value)->toBe(70.0);
});

test('TitleScoreContributionBuilder penalizes short title warning', function () {
    $builder = new TitleScoreContributionBuilder();
    $warnings = [
        new AnalysisWarning('Title is too short', 'Too short.', sourceCheckId: 'seo.title'),
    ];
    $score = $builder->build([], $warnings, []);

    expect($score->value)->toBe(85.0);
});

test('TitleScoreContributionBuilder penalizes long title warning', function () {
    $builder = new TitleScoreContributionBuilder();
    $warnings = [
        new AnalysisWarning('Title is too long', 'Too long.', sourceCheckId: 'seo.title'),
    ];
    $score = $builder->build([], $warnings, []);

    expect($score->value)->toBe(90.0);
});

test('TitleScoreContributionBuilder penalizes missing focus keyword', function () {
    $builder = new TitleScoreContributionBuilder();
    $suggestions = [
        new AnalysisSuggestion(
            'Focus keyword "seo" not found in title',
            'Missing keyword.',
            sourceCheckId: 'seo.title',
        ),
    ];
    $score = $builder->build([], [], $suggestions);

    expect($score->value)->toBe(95.0);
});

test('TitleScoreContributionBuilder penalizes duplicate title', function () {
    $builder = new TitleScoreContributionBuilder();
    $suggestions = [
        new AnalysisSuggestion(
            'Duplicate title detected',
            'Title matches another page.',
            sourceCheckId: 'seo.title',
        ),
    ];
    $score = $builder->build([], [], $suggestions);

    expect($score->value)->toBe(92.0);
});

test('TitleScoreContributionBuilder accumulates deductions', function () {
    $builder = new TitleScoreContributionBuilder();
    $issues = [
        new AnalysisIssue('Title is missing', '', sourceCheckId: 'seo.title'),
    ];
    $warnings = [
        new AnalysisWarning('Title is too short', '', sourceCheckId: 'seo.title'),
    ];
    $suggestions = [
        new AnalysisSuggestion('Focus keyword "x" not found in title', '', sourceCheckId: 'seo.title'),
    ];

    $score = $builder->build($issues, $warnings, $suggestions);

    expect($score->value)->toBe(40.0); // 100 - 40 - 15 - 5
    expect($score->contributors)->toHaveCount(3);
    expect($score->metadata['total_deductions'])->toBe(60.0);
});

test('TitleScoreContributionBuilder never goes below zero', function () {
    $builder = new TitleScoreContributionBuilder();
    $issues = [
        new AnalysisIssue('Title is missing', '', sourceCheckId: 'seo.title'),
        new AnalysisIssue('Title is empty', '', sourceCheckId: 'seo.title'),
        new AnalysisIssue('Title contains only punctuation', '', sourceCheckId: 'seo.title'),
    ];
    $warnings = [
        new AnalysisWarning('Title is too short', '', sourceCheckId: 'seo.title'),
        new AnalysisWarning('Title is too long', '', sourceCheckId: 'seo.title'),
    ];

    $score = $builder->build($issues, $warnings, []);

    expect($score->value)->toBe(0.0);
});

test('TitleScoreContributionBuilder includes rationale metadata', function () {
    $builder = new TitleScoreContributionBuilder();
    $issues = [
        new AnalysisIssue('Title is missing', '', sourceCheckId: 'seo.title'),
    ];
    $score = $builder->build($issues, [], []);

    expect($score->metadata)->toHaveKey('rationale');
    expect($score->metadata)->toHaveKey('max_score');
    expect($score->metadata)->toHaveKey('total_deductions');
    expect($score->metadata['max_score'])->toBe(100.0);
    expect($score->metadata['total_deductions'])->toBe(40.0);
});

test('TitleScoreContributionBuilder is deterministic', function () {
    $builder = new TitleScoreContributionBuilder();
    $issues = [
        new AnalysisIssue('Title is missing', '', sourceCheckId: 'seo.title'),
    ];

    $score1 = $builder->build($issues, [], []);
    $score2 = $builder->build($issues, [], []);

    expect($score1->value)->toBe($score2->value);
    expect($score1->contributors)->toBe($score2->contributors);
    expect($score1->metadata)->toBe($score2->metadata);
});

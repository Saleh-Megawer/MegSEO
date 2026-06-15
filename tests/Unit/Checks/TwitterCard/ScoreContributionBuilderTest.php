<?php

declare(strict_types=1);

use MegSEO\Checks\TwitterCard\Scoring\TwitterCardScoreContributionBuilder;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\AnalysisSuggestion;

test('Clean card gives max score', function () {
    expect((new TwitterCardScoreContributionBuilder())->build([],[],[])->value)->toBe(100.0);
});

test('Missing card penalty', function () {
    $s = (new TwitterCardScoreContributionBuilder())->build([new AnalysisIssue('twitter:card is missing','','seo.twitter_card')],[],[]);
    expect($s->value)->toBe(80.0);
});

test('Empty card penalty', function () {
    $s = (new TwitterCardScoreContributionBuilder())->build([new AnalysisIssue('twitter:card is empty','','seo.twitter_card')],[],[]);
    expect($s->value)->toBe(85.0);
});

test('Invalid type penalty', function () {
    $s = (new TwitterCardScoreContributionBuilder())->build([],[new AnalysisWarning('Invalid twitter:card type','','seo.twitter_card')],[]);
    expect($s->value)->toBe(90.0);
});

test('Relative image penalty', function () {
    $s = (new TwitterCardScoreContributionBuilder())->build([],[new AnalysisWarning('twitter:image URL is relative','','seo.twitter_card')],[]);
    expect($s->value)->toBe(90.0);
});

test('Conflict penalty', function () {
    $s = (new TwitterCardScoreContributionBuilder())->build([],[],[new AnalysisSuggestion('Conflicting twitter:card values','','seo.twitter_card')]);
    expect($s->value)->toBe(85.0);
});

test('Rationale metadata present', function () {
    $s = (new TwitterCardScoreContributionBuilder())->build([new AnalysisIssue('twitter:card is missing','','seo.twitter_card')],[],[]);
    expect($s->metadata)->toHaveKey('rationale');
    expect($s->metadata)->toHaveKey('total_deductions');
});

<?php

declare(strict_types=1);

use MegSEO\Checks\Hreflang\Scoring\HreflangScoreContributionBuilder;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\AnalysisSuggestion;

test('Clean gives max', function () { expect((new HreflangScoreContributionBuilder())->build([],[],[])->value)->toBe(100.0); });
test('Missing penalty', function () { $s = (new HreflangScoreContributionBuilder())->build([new AnalysisIssue('Hreflang data is missing','','seo.hreflang')],[],[]); expect($s->value)->toBe(60.0); });
test('Empty penalty', function () { $s = (new HreflangScoreContributionBuilder())->build([new AnalysisIssue('hreflang value is empty','','seo.hreflang')],[],[]); expect($s->value)->toBe(75.0); });
test('Invalid lang penalty', function () { $s = (new HreflangScoreContributionBuilder())->build([],[new AnalysisWarning('Invalid hreflang language code','','seo.hreflang')],[]); expect($s->value)->toBe(85.0); });
test('Relative URL penalty', function () { $s = (new HreflangScoreContributionBuilder())->build([],[new AnalysisWarning('hreflang href is relative','','seo.hreflang')],[]); expect($s->value)->toBe(90.0); });
test('x-default penalty', function () { $s = (new HreflangScoreContributionBuilder())->build([],[],[new AnalysisSuggestion('x-default hreflang entry is missing','','seo.hreflang')]); expect($s->value)->toBe(95.0); });
test('Duplicate lang penalty', function () { $s = (new HreflangScoreContributionBuilder())->build([],[],[new AnalysisSuggestion('Duplicate hreflang language','','seo.hreflang')]); expect($s->value)->toBe(95.0); });
test('Same href penalty', function () { $s = (new HreflangScoreContributionBuilder())->build([],[new AnalysisWarning('Same href used for multiple languages','','seo.hreflang')],[]); expect($s->value)->toBe(90.0); });
test('Rationale present', function () { $s = (new HreflangScoreContributionBuilder())->build([new AnalysisIssue('Hreflang data is missing','','seo.hreflang')],[],[]); expect($s->metadata)->toHaveKey('rationale'); });

<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\Rules;

use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingTwitterTitle
{
    public function evaluate(TwitterCardCheckInput $input, bool $isEmptyDetected): ?AnalysisIssue
    {
        if ($isEmptyDetected) return null;
        if (! $input->has('twitter:title')) {
            return new AnalysisIssue('twitter:title is missing', 'The twitter:title tag provides the title shown in Twitter/X link previews.', 'seo.twitter_card');
        }
        return null;
    }
}

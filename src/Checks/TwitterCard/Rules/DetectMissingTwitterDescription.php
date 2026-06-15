<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\Rules;

use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingTwitterDescription
{
    public function evaluate(TwitterCardCheckInput $input, bool $isEmptyDetected): ?AnalysisIssue
    {
        if ($isEmptyDetected) return null;
        if (! $input->has('twitter:description')) {
            return new AnalysisIssue('twitter:description is missing', 'The twitter:description tag provides the description shown in Twitter/X link previews.', 'seo.twitter_card');
        }
        return null;
    }
}

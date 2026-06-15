<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\Rules;

use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingTwitterImage
{
    public function evaluate(TwitterCardCheckInput $input, bool $isEmptyDetected): ?AnalysisIssue
    {
        if ($isEmptyDetected) return null;
        if (! $input->has('twitter:image')) {
            return new AnalysisIssue('twitter:image is missing', 'The twitter:image tag provides the image shown in Twitter/X link previews.', 'seo.twitter_card');
        }
        return null;
    }
}

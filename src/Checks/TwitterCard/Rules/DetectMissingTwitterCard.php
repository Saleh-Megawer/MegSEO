<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\Rules;

use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingTwitterCard
{
    public function evaluate(TwitterCardCheckInput $input, bool $isEmptyDetected): ?AnalysisIssue
    {
        if ($isEmptyDetected) return null;
        if (! $input->has('twitter:card')) {
            return new AnalysisIssue('twitter:card is missing', 'The twitter:card tag is required. It defines the card type used by Twitter/X for link previews.', 'seo.twitter_card');
        }
        return null;
    }
}

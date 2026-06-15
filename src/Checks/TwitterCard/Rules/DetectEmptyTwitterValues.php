<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\Rules;

use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectEmptyTwitterValues
{
    /** @return array<int, AnalysisIssue> */
    public function evaluate(TwitterCardCheckInput $input): array
    {
        $issues = [];
        $keys = ['twitter:card', 'twitter:title', 'twitter:description', 'twitter:image'];
        foreach ($keys as $key) {
            if ($input->isEmpty($key)) {
                $label = str_replace('twitter:', '', $key);
                $issues[] = new AnalysisIssue("twitter:{$label} is empty", "The {$key} tag is present but its value is empty. Provide a meaningful value for proper Twitter/X link previews.", 'seo.twitter_card');
            }
        }
        return $issues;
    }
}

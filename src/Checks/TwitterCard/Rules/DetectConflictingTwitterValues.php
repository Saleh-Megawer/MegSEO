<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\Rules;

use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;
use MegSEO\DTO\AnalysisSuggestion;

final readonly class DetectConflictingTwitterValues
{
    /** @return array<int, AnalysisSuggestion> */
    public function evaluate(TwitterCardCheckInput $input): array
    {
        $suggestions = [];
        $keys = ['twitter:card', 'twitter:title', 'twitter:description', 'twitter:image'];

        foreach ($keys as $key) {
            $values = $input->getArray($key);
            if (count($values) <= 1) continue;

            $unique = array_unique($values);
            if (count($unique) <= 1) continue;

            $label = str_replace('twitter:', '', $key);
            $suggestions[] = new AnalysisSuggestion("Conflicting {$key} values detected", "Multiple different values were found for {$key}. Only one value should be present.", 'seo.twitter_card', confidence: 0.9);
        }

        return $suggestions;
    }
}

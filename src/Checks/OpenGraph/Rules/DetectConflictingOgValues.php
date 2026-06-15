<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph\Rules;

use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;
use MegSEO\DTO\AnalysisSuggestion;

final readonly class DetectConflictingOgValues
{
    /** @return array<int, AnalysisSuggestion> */
    public function evaluate(OpenGraphCheckInput $input): array
    {
        $suggestions = [];
        $keys = ['og:title', 'og:description', 'og:image'];

        foreach ($keys as $key) {
            $values = $input->getArray($key);
            if (count($values) <= 1) {
                continue;
            }

            $unique = array_unique($values);
            if (count($unique) <= 1) {
                continue;
            }

            $label = str_replace('og:', '', $key);
            $suggestions[] = new AnalysisSuggestion(
                message: "Conflicting {$key} values detected",
                details: "Multiple different values were found for {$key}. Only one value should be present to avoid ambiguity for social platforms.",
                sourceCheckId: 'seo.open_graph',
                confidence: 0.9,
            );
        }

        return $suggestions;
    }
}

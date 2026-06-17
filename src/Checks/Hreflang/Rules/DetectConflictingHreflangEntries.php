<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\Rules;

use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;

final readonly class DetectConflictingHreflangEntries
{
    /** @return array<int, AnalysisSuggestion|AnalysisWarning> */
    public function evaluate(HreflangCheckInput $input): array
    {
        $findings = [];

        // Duplicate language codes → suggestion
        $langCounts = [];
        foreach ($input->getEntries() as $entry) {
            $lang = isset($entry['hreflang']) ? trim((string) $entry['hreflang']) : '';
            if ($lang !== '') {
                $langCounts[$lang] = ($langCounts[$lang] ?? 0) + 1;
            }
        }
        foreach ($langCounts as $lang => $count) {
            if ($count > 1) {
                $findings[] = new AnalysisSuggestion(
                    "Duplicate hreflang language: {$lang}",
                    "The language code \"{$lang}\" appears {$count} times. Each language should have only one hreflang entry.",
                    'seo.hreflang',
                    confidence: 0.9,
                );
            }
        }

        // Same href for different languages → warning
        $hrefToLangs = [];
        foreach ($input->getEntries() as $entry) {
            $href = isset($entry['href']) ? trim((string) $entry['href']) : '';
            $lang = isset($entry['hreflang']) ? trim((string) $entry['hreflang']) : '';
            if ($href !== '' && $lang !== '') {
                $hrefToLangs[$href][] = $lang;
            }
        }
        foreach ($hrefToLangs as $href => $langs) {
            $uniqueLangs = array_unique($langs);
            if (count($uniqueLangs) > 1) {
                $findings[] = new AnalysisWarning(
                    'Same href used for multiple languages',
                    'The URL "' . $href . '" is assigned to multiple languages: ' . implode(', ', $uniqueLangs) . '. Each language should point to its own URL.',
                    'seo.hreflang',
                );
            }
        }

        return $findings;
    }
}

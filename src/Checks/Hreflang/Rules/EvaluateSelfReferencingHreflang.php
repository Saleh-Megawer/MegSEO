<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\Rules;

use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;
use MegSEO\DTO\AnalysisSuggestion;

final readonly class EvaluateSelfReferencingHreflang
{
    /**
     * @param HreflangCheckInput $input
     * @param string|null $pageUrl The current page URL
     * @param string|null $pageLanguage The current page language code
     * @return array<int, AnalysisSuggestion>
     */
    public function evaluate(HreflangCheckInput $input, ?string $pageUrl, ?string $pageLanguage): array
    {
        if ($pageUrl === null || $pageLanguage === null || $pageUrl === '' || $pageLanguage === '') {
            return [];
        }

        $suggestions = [];

        foreach ($input->getEntries() as $idx => $entry) {
            $lang = isset($entry['hreflang']) ? trim((string) $entry['hreflang']) : '';
            $href = isset($entry['href']) ? trim((string) $entry['href']) : '';

            if ($lang === '' || $href === '') continue;
            if ($lang !== $pageLanguage) continue;

            // This entry matches page language — must self-reference
            if ($href !== $pageUrl) {
                $suggestions[] = new AnalysisSuggestion(
                    "hreflang self-reference mismatch for {$lang}",
                    "The hreflang entry for language \"{$lang}\" points to \"{$href}\" but the current page URL is \"{$pageUrl}\". The entry matching the page language should self-reference.",
                    'seo.hreflang',
                    confidence: 0.85,
                );
            }
        }

        return $suggestions;
    }
}

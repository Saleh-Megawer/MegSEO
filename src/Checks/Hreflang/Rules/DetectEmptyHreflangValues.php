<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\Rules;

use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectEmptyHreflangValues
{
    /** @return array<int, AnalysisIssue> */
    public function evaluate(HreflangCheckInput $input): array
    {
        $issues = [];
        foreach ($input->getEntries() as $i => $entry) {
            $lang = isset($entry['hreflang']) ? trim((string) $entry['hreflang']) : '';
            $href = isset($entry['href']) ? trim((string) $entry['href']) : '';

            if ($lang === '') {
                $issues[] = new AnalysisIssue("hreflang value is empty (entry {$i})", 'The hreflang language code is empty. Each hreflang entry requires a valid language code.', 'seo.hreflang');
            }
            if ($href === '') {
                $issues[] = new AnalysisIssue("hreflang href is empty (entry {$i})", 'The hreflang href URL is empty. Each hreflang entry requires a valid absolute URL.', 'seo.hreflang');
            }
        }
        return $issues;
    }
}

<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\Rules;

use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;
use MegSEO\DTO\AnalysisSuggestion;

final readonly class DetectMissingXDefault
{
    public function evaluate(HreflangCheckInput $input): ?AnalysisSuggestion
    {
        if ($input->entryCount() < 2) return null;

        foreach ($input->getEntries() as $entry) {
            if (isset($entry['hreflang']) && trim((string) $entry['hreflang']) === 'x-default') {
                return null;
            }
        }

        return new AnalysisSuggestion(
            'x-default hreflang entry is missing',
            'No x-default entry found. An x-default entry helps search engines determine the fallback page for users whose language is not explicitly listed.',
            'seo.hreflang',
            confidence: 0.8,
        );
    }
}

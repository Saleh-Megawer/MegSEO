<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\Rules;

use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingHreflang
{
    public function evaluate(HreflangCheckInput $input): ?AnalysisIssue
    {
        if (! $input->hasEntries()) {
            return new AnalysisIssue('Hreflang data is missing', 'No hreflang tags were found. Hreflang annotations help search engines serve the correct language version of a page.', 'seo.hreflang');
        }
        return null;
    }
}

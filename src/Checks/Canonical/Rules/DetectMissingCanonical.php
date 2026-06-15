<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Rules;

use MegSEO\Checks\Canonical\DTO\CanonicalUrlNormalizationResult;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingCanonical
{
    public function evaluate(?CanonicalUrlNormalizationResult $normalized): ?AnalysisIssue
    {
        if ($normalized === null || $normalized->rawCanonical === null) {
            return new AnalysisIssue(
                message: 'Canonical tag is missing',
                details: 'No canonical URL data was supplied. A canonical tag helps search engines understand the preferred version of a page, preventing duplicate content issues.',
                sourceCheckId: 'seo.canonical',
            );
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Rules;

use MegSEO\Checks\Canonical\DTO\CanonicalCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMultipleCanonicals
{
    public function evaluate(CanonicalCheckInput $input): ?AnalysisIssue
    {
        if ($input->hasMultipleCanonicals()) {
            $count = count($input->canonicalUrls);
            return new AnalysisIssue(
                message: 'Multiple canonical tags detected',
                details: "{$count} canonical tags were detected on this page. Only one canonical tag should be present to avoid confusing search engines.",
                sourceCheckId: 'seo.canonical',
            );
        }

        return null;
    }
}

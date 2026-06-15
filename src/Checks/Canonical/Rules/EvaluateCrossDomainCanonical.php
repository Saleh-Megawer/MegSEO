<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Rules;

use MegSEO\Checks\Canonical\DTO\CanonicalUrlMatchReport;
use MegSEO\DTO\AnalysisSuggestion;

final readonly class EvaluateCrossDomainCanonical
{
    public function evaluate(CanonicalUrlMatchReport $report): ?AnalysisSuggestion
    {
        if (! $report->isCrossDomain) {
            return null;
        }

        return new AnalysisSuggestion(
            message: 'Canonical URL points to a different domain',
            details: 'The canonical URL points to a different domain than the page URL. Cross-domain canonicals are valid for syndicated or duplicated content, but verify this is intentional.',
            sourceCheckId: 'seo.canonical',
            confidence: 0.8,
        );
    }
}

<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Rules;

use MegSEO\Checks\Canonical\DTO\CanonicalUrlMatchReport;
use MegSEO\DTO\AnalysisSuggestion;

final readonly class EvaluateSelfReferencingCanonical
{
    public function evaluate(CanonicalUrlMatchReport $report): ?AnalysisSuggestion
    {
        // Skip when page URL is unavailable (cannot determine self-referencing)
        if ($report->isSelfReferencing || $report->isRelative || $report->isCrossDomain || ! $report->hasPageUrl) {
            return null;
        }

        $detail = $report->matchDetails ?: 'The canonical URL does not match the page URL. While this may be intentional for cross-domain content, a self-referencing canonical is recommended for original content to help search engines identify the preferred URL.';

        return new AnalysisSuggestion(
            message: 'Canonical URL does not self-reference the page',
            details: $detail,
            sourceCheckId: 'seo.canonical',
            confidence: 0.7,
        );
    }
}

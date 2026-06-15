<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Rules;

use MegSEO\Checks\Canonical\DTO\CanonicalUrlMatchReport;
use MegSEO\DTO\AnalysisWarning;

final readonly class EvaluateRelativeCanonicalUrl
{
    public function evaluate(CanonicalUrlMatchReport $report): ?AnalysisWarning
    {
        if (! $report->isRelative) {
            return null;
        }

        return new AnalysisWarning(
            message: 'Canonical URL is relative',
            details: 'The canonical URL is a relative path. Search engines strongly recommend absolute canonical URLs to avoid ambiguity. Use a full URL including the scheme and hostname.',
            sourceCheckId: 'seo.canonical',
        );
    }
}

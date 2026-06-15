<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Rules;

use MegSEO\Checks\Canonical\DTO\CanonicalUrlNormalizationResult;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectEmptyCanonical
{
    public function evaluate(CanonicalUrlNormalizationResult $normalized): ?AnalysisIssue
    {
        if ($normalized->normalizedCanonical === '' || $normalized->normalizedCanonical === null) {
            return new AnalysisIssue(
                message: 'Canonical tag is empty',
                details: 'The canonical tag value is empty. Every page should have a valid, well-formed canonical URL.',
                sourceCheckId: 'seo.canonical',
            );
        }

        return null;
    }
}

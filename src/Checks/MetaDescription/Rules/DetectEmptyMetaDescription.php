<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Rules;

use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectEmptyMetaDescription
{
    public function evaluate(MetaDescriptionNormalizationResult $normalized): ?AnalysisIssue
    {
        if ($normalized->normalizedDescription === '' || $normalized->normalizedDescription === null) {
            return new AnalysisIssue(
                message: 'Meta description is empty',
                details: 'The submitted meta description is empty. Every page should have a meaningful, descriptive meta description for search engine result snippets.',
                sourceCheckId: 'seo.meta_description',
            );
        }

        return null;
    }
}

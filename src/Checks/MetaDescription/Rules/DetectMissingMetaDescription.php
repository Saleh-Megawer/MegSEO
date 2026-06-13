<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Rules;

use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingMetaDescription
{
    public function evaluate(?MetaDescriptionNormalizationResult $normalized): ?AnalysisIssue
    {
        if ($normalized === null || $normalized->rawDescription === null) {
            return new AnalysisIssue(
                message: 'Meta description is missing',
                details: 'No meta description data was supplied for analysis. A meta description is important for search engine result snippets and user click-through.',
                sourceCheckId: 'seo.meta_description',
            );
        }

        return null;
    }
}

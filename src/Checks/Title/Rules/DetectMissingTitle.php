<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Rules;

use MegSEO\Checks\Title\DTO\TitleNormalizationResult;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingTitle
{
    public function evaluate(?TitleNormalizationResult $normalized): ?AnalysisIssue
    {
        if ($normalized === null || $normalized->rawTitle === null) {
            return new AnalysisIssue(
                message: 'Title is missing',
                details: 'No title data was supplied for analysis. A page title is essential for SEO and user experience.',
                sourceCheckId: 'seo.title',
            );
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Rules;

use MegSEO\Checks\Title\DTO\TitleNormalizationResult;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectEmptyTitle
{
    public function evaluate(TitleNormalizationResult $normalized): ?AnalysisIssue
    {
        if ($normalized->normalizedTitle === '' || $normalized->normalizedTitle === null) {
            return new AnalysisIssue(
                message: 'Title is empty',
                details: 'The submitted title is empty. Every page should have a meaningful, descriptive title.',
                sourceCheckId: 'seo.title',
            );
        }

        return null;
    }
}

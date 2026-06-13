<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Rules;

use MegSEO\Checks\Title\DTO\TitleNormalizationResult;
use MegSEO\Checks\Title\Support\TitleLengthPolicy;
use MegSEO\DTO\AnalysisWarning;

final readonly class EvaluateTitleLength
{
    public function __construct(
        private TitleLengthPolicy $policy,
    ) {}

    public function evaluate(TitleNormalizationResult $normalized): ?AnalysisWarning
    {
        $title = $normalized->normalizedTitle;
        if ($title === null || $title === '') {
            return null;
        }

        $length = mb_strlen($title, 'UTF-8');

        if ($this->policy->isShort($length)) {
            return new AnalysisWarning(
                message: 'Title is too short',
                details: "The title is {$length} characters long, which is below the recommended minimum of {$this->policy->getRecommendedMin()} characters. A longer, more descriptive title can improve search visibility.",
                sourceCheckId: 'seo.title',
            );
        }

        if ($this->policy->isLong($length)) {
            return new AnalysisWarning(
                message: 'Title is too long',
                details: "The title is {$length} characters long, which exceeds the recommended maximum of {$this->policy->getRecommendedMax()} characters. Search engines may truncate long titles in results.",
                sourceCheckId: 'seo.title',
            );
        }

        return null;
    }
}

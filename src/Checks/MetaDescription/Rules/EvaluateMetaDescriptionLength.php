<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Rules;

use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;
use MegSEO\Checks\MetaDescription\Support\MetaDescriptionLengthPolicy;
use MegSEO\DTO\AnalysisWarning;

final readonly class EvaluateMetaDescriptionLength
{
    public function __construct(
        private MetaDescriptionLengthPolicy $policy,
    ) {}

    public function evaluate(MetaDescriptionNormalizationResult $normalized): ?AnalysisWarning
    {
        $desc = $normalized->normalizedDescription;
        if ($desc === null || $desc === '') {
            return null;
        }

        $length = mb_strlen($desc, 'UTF-8');

        if ($this->policy->isShort($length)) {
            return new AnalysisWarning(
                message: 'Meta description is too short',
                details: "The meta description is {$length} characters long, below the recommended minimum of {$this->policy->getRecommendedMin()}. A longer description provides more opportunity to engage users.",
                sourceCheckId: 'seo.meta_description',
            );
        }

        if ($this->policy->isLong($length)) {
            return new AnalysisWarning(
                message: 'Meta description is too long',
                details: "The meta description is {$length} characters long, exceeding the recommended maximum of {$this->policy->getRecommendedMax()}. Search engines may truncate long descriptions in results.",
                sourceCheckId: 'seo.meta_description',
            );
        }

        return null;
    }
}

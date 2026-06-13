<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Rules;

use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;
use MegSEO\Checks\MetaDescription\Support\MetaDescriptionCharacterClassifier;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectSeparatorOnlyMetaDescription
{
    public function __construct(
        private MetaDescriptionCharacterClassifier $classifier = new MetaDescriptionCharacterClassifier(),
    ) {}

    public function evaluate(MetaDescriptionNormalizationResult $normalized): ?AnalysisIssue
    {
        $desc = $normalized->normalizedDescription;
        if ($desc === null || $desc === '') {
            return null;
        }

        if (! $this->classifier->containsMeaningfulText($desc)) {
            return new AnalysisIssue(
                message: 'Meta description contains only punctuation, separators, or whitespace',
                details: 'The submitted meta description has no meaningful content.',
                sourceCheckId: 'seo.meta_description',
            );
        }

        return null;
    }
}

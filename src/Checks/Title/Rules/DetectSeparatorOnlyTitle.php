<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Rules;

use MegSEO\Checks\Title\DTO\TitleNormalizationResult;
use MegSEO\Checks\Title\Support\TitleCharacterClassifier;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectSeparatorOnlyTitle
{
    public function __construct(
        private TitleCharacterClassifier $classifier = new TitleCharacterClassifier(),
    ) {}

    public function evaluate(TitleNormalizationResult $normalized): ?AnalysisIssue
    {
        $title = $normalized->normalizedTitle;
        if ($title === null || $title === '') {
            return null;
        }

        if (! $this->classifier->containsMeaningfulText($title)) {
            return new AnalysisIssue(
                message: 'Title contains only punctuation, separators, or whitespace',
                details: 'The submitted title has no meaningful content. It contains only punctuation marks, separators, or whitespace characters.',
                sourceCheckId: 'seo.title',
            );
        }

        return null;
    }
}

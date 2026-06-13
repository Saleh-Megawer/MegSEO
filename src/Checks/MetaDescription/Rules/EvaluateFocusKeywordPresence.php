<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Rules;

use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;
use MegSEO\DTO\AnalysisSuggestion;

final readonly class EvaluateFocusKeywordPresence
{
    public function evaluate(MetaDescriptionNormalizationResult $normalized): ?AnalysisSuggestion
    {
        $keyword = $normalized->normalizedFocusKeyword;

        if ($keyword === null || $keyword === '') {
            return null;
        }

        $desc = $normalized->normalizedDescription;
        if ($desc === null || $desc === '') {
            return new AnalysisSuggestion(
                message: 'Focus keyword not found — description is empty',
                details: "The focus keyword \"{$keyword}\" was supplied but the meta description is missing or empty. Include your target keyword in a meaningful description to improve search result relevance.",
                sourceCheckId: 'seo.meta_description',
                confidence: 1.0,
            );
        }

        if (mb_stripos($desc, $keyword) !== false) {
            return null;
        }

        return new AnalysisSuggestion(
            message: "Focus keyword \"{$keyword}\" not found in meta description",
            details: "The focus keyword \"{$keyword}\" does not appear in the meta description. Including your target keyword helps search engines and users understand the page's topic at a glance.",
            sourceCheckId: 'seo.meta_description',
            confidence: 0.85,
        );
    }
}

<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Rules;

use MegSEO\Checks\Title\DTO\TitleNormalizationResult;
use MegSEO\DTO\AnalysisSuggestion;

final readonly class EvaluateFocusKeywordPresence
{
    public function evaluate(TitleNormalizationResult $normalized): ?AnalysisSuggestion
    {
        $keyword = $normalized->normalizedFocusKeyword;

        if ($keyword === null || $keyword === '') {
            return null;
        }

        $title = $normalized->normalizedTitle;
        if ($title === null || $title === '') {
            return new AnalysisSuggestion(
                message: 'Focus keyword not found — title is empty',
                details: "The focus keyword \"{$keyword}\" was supplied but the title is missing or empty. Add a meaningful title that incorporates your target keyword to improve search relevance.",
                sourceCheckId: 'seo.title',
                confidence: 1.0,
            );
        }

        if (mb_stripos($title, $keyword) !== false) {
            return null;
        }

        return new AnalysisSuggestion(
            message: "Focus keyword \"{$keyword}\" not found in title",
            details: "The focus keyword \"{$keyword}\" does not appear in the page title \"{$title}\". Including your target keyword in the title helps search engines and users understand the page's topic at a glance.",
            sourceCheckId: 'seo.title',
            confidence: 0.85,
        );
    }
}

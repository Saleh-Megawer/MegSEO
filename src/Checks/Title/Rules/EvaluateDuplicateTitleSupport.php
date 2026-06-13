<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Rules;

use MegSEO\Checks\Title\DTO\TitleDuplicateMatch;
use MegSEO\DTO\AnalysisSuggestion;

final readonly class EvaluateDuplicateTitleSupport
{
    /**
     * @param array<int, TitleDuplicateMatch> $matches
     */
    public function evaluate(string $normalizedTitle, array $matches): ?AnalysisSuggestion
    {
        if ($normalizedTitle === '' || $matches === []) {
            return null;
        }

        $count = count($matches);

        if ($count === 1) {
            $match = $matches[0];
            return new AnalysisSuggestion(
                message: 'Duplicate title detected',
                details: "The title \"{$normalizedTitle}\" matches another page at {$match->matchedReference}. Unique titles help search engines distinguish pages and improve indexing.",
                sourceCheckId: 'seo.title',
                confidence: 0.9,
            );
        }

        $references = array_map(
            fn (TitleDuplicateMatch $m) => $m->matchedReference,
            $matches,
        );
        $refList = implode(', ', $references);

        return new AnalysisSuggestion(
            message: "Duplicate title detected across {$count} pages",
            details: "The title \"{$normalizedTitle}\" matches {$count} other pages ({$refList}). Each page should have a unique, descriptive title to help search engines understand your site structure and avoid keyword cannibalization.",
            sourceCheckId: 'seo.title',
            confidence: 0.95,
        );
    }
}

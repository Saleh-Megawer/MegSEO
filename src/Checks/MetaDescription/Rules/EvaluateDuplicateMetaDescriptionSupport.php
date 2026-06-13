<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Rules;

use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionDuplicateMatch;
use MegSEO\DTO\AnalysisSuggestion;

final readonly class EvaluateDuplicateMetaDescriptionSupport
{
    /**
     * @param array<int, MetaDescriptionDuplicateMatch> $matches
     */
    public function evaluate(string $normalizedDescription, array $matches): ?AnalysisSuggestion
    {
        if ($normalizedDescription === '' || $matches === []) {
            return null;
        }

        $count = count($matches);

        if ($count === 1) {
            $match = $matches[0];
            return new AnalysisSuggestion(
                message: 'Duplicate meta description detected',
                details: "The meta description matches another page at {$match->matchedReference}. Unique descriptions help search engines distinguish pages and improve click-through rates.",
                sourceCheckId: 'seo.meta_description',
                confidence: 0.9,
            );
        }

        $references = array_map(fn (MetaDescriptionDuplicateMatch $m) => $m->matchedReference, $matches);
        $refList = implode(', ', $references);

        return new AnalysisSuggestion(
            message: "Duplicate meta description detected across {$count} pages",
            details: "The meta description matches {$count} other pages ({$refList}). Each page should have a unique, descriptive meta description.",
            sourceCheckId: 'seo.meta_description',
            confidence: 0.95,
        );
    }
}

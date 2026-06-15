<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\Rules;

use MegSEO\DTO\AnalysisWarning;

final readonly class EvaluateTwitterCardType
{
    private const SUPPORTED = ['summary', 'summary_large_image', 'app', 'player'];

    public function evaluate(?string $type, bool $isEmptyDetected): ?AnalysisWarning
    {
        if ($isEmptyDetected || $type === null) return null;

        if (! in_array(strtolower($type), self::SUPPORTED, true)) {
            return new AnalysisWarning(
                message: 'Invalid twitter:card type',
                details: "The twitter:card value \"{$type}\" is not a supported card type. Supported types: summary, summary_large_image, app, player. Twitter may fall back to a generic card.",
                sourceCheckId: 'seo.twitter_card',
            );
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Rules;

use MegSEO\Checks\Canonical\DTO\CanonicalUrlNormalizationResult;
use MegSEO\Checks\Canonical\Support\CanonicalUrlValidator;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectInvalidCanonicalUrl
{
    public function __construct(
        private CanonicalUrlValidator $validator = new CanonicalUrlValidator(),
    ) {}

    public function evaluate(CanonicalUrlNormalizationResult $normalized): ?AnalysisIssue
    {
        $url = $normalized->rawCanonical;
        if ($url === null || $url === '') {
            return null;
        }

        // Relative URLs are handled by EvaluateRelativeCanonicalUrl, not this rule
        if ($this->validator->isRelative($url)) {
            return null;
        }

        if (! $this->validator->isValid($url)) {
            return new AnalysisIssue(
                message: 'Canonical URL is invalid',
                details: "The canonical URL \"{$url}\" is not a valid absolute URL. Canonical URLs must use http or https scheme with a valid hostname.",
                sourceCheckId: 'seo.canonical',
            );
        }

        return null;
    }
}

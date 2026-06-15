<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph\Rules;

use MegSEO\Checks\Canonical\Support\CanonicalUrlValidator;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;

final readonly class EvaluateOgImageUrl
{
    public function __construct(
        private CanonicalUrlValidator $validator = new CanonicalUrlValidator(),
    ) {}

    /**
     * @return array{0?: AnalysisWarning, 1?: AnalysisSuggestion}
     */
    public function evaluate(string $url): array
    {
        $findings = [];

        if ($this->validator->isRelative($url)) {
            $findings[] = new AnalysisWarning(
                message: 'og:image URL is relative',
                details: 'The og:image URL is relative. Use an absolute URL so social platforms can reliably fetch and display the image.',
                sourceCheckId: 'seo.open_graph',
            );
        } elseif (! $this->validator->isValid($url)) {
            $findings[] = new AnalysisWarning(
                message: 'og:image URL is invalid',
                details: "The og:image URL \"{$url}\" is not a valid absolute URL. Social platforms cannot fetch images from invalid URLs.",
                sourceCheckId: 'seo.open_graph',
            );
        }

        return $findings;
    }
}

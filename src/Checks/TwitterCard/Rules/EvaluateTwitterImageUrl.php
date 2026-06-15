<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\Rules;

use MegSEO\Checks\Canonical\Support\CanonicalUrlValidator;
use MegSEO\DTO\AnalysisWarning;

final readonly class EvaluateTwitterImageUrl
{
    public function __construct(private CanonicalUrlValidator $validator = new CanonicalUrlValidator()) {}

    /** @return array<int, AnalysisWarning> */
    public function evaluate(string $url): array
    {
        $findings = [];

        if ($this->validator->isRelative($url)) {
            $findings[] = new AnalysisWarning('twitter:image URL is relative', 'The twitter:image URL is relative. Use an absolute URL so Twitter/X can reliably fetch and display the image.', 'seo.twitter_card');
        } elseif (! $this->validator->isValid($url)) {
            $findings[] = new AnalysisWarning('twitter:image URL is invalid', "The twitter:image URL \"{$url}\" is not a valid absolute URL. Twitter/X cannot fetch images from invalid URLs.", 'seo.twitter_card');
        }

        return $findings;
    }
}

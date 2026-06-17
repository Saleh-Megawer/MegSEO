<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\Rules;

use MegSEO\Checks\Canonical\Support\CanonicalUrlValidator;
use MegSEO\DTO\AnalysisWarning;

final readonly class ValidateHreflangUrl
{
    public function __construct(private CanonicalUrlValidator $validator = new CanonicalUrlValidator()) {}

    public function evaluate(?string $href, bool $isEmptyDetected): ?AnalysisWarning
    {
        if ($isEmptyDetected || $href === null || $href === '') return null;

        if ($this->validator->isRelative($href)) {
            return new AnalysisWarning('hreflang href is relative', 'The hreflang href URL is relative. Use absolute URLs so search engines can resolve the correct page.', 'seo.hreflang');
        }

        if (! $this->validator->isValid($href)) {
            return new AnalysisWarning('hreflang href is invalid', "The hreflang href \"{$href}\" is not a valid absolute URL.", 'seo.hreflang');
        }

        return null;
    }
}

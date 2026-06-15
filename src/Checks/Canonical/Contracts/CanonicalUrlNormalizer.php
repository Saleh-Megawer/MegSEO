<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Contracts;

use MegSEO\Checks\Canonical\DTO\CanonicalUrlNormalizationResult;

interface CanonicalUrlNormalizer
{
    public function normalize(?string $canonicalUrl, ?string $pageUrl = null): CanonicalUrlNormalizationResult;
}

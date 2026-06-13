<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Contracts;

use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;

interface MetaDescriptionNormalizer
{
    public function normalize(?string $rawDescription, ?string $focusKeyword = null): MetaDescriptionNormalizationResult;
}

<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Contracts;

use MegSEO\Checks\Title\DTO\TitleNormalizationResult;

interface TitleNormalizer
{
    public function normalize(?string $rawTitle, ?string $focusKeyword = null): TitleNormalizationResult;
}

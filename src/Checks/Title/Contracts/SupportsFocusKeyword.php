<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Contracts;

interface SupportsFocusKeyword
{
    public function keywordSupplied(): bool;

    public function getNormalizedKeyword(): ?string;
}

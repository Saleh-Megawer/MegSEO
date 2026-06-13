<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Contracts;

interface SupportsFocusKeyword
{
    public function keywordSupplied(): bool;

    public function getNormalizedKeyword(): ?string;
}

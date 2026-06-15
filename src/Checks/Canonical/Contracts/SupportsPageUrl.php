<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Contracts;

interface SupportsPageUrl
{
    public function pageUrlSupplied(): bool;

    public function getNormalizedPageUrl(): ?string;
}

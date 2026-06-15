<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\DTO;

final readonly class CanonicalCheckInput
{
    /** @var array<int, string> */
    public array $canonicalUrls;

    public function __construct(
        public ?string $canonical,
        array $canonicalUrls = [],
        public ?string $pageUrl = null,
    ) {
        $this->canonicalUrls = $canonicalUrls;
    }

    public function hasCanonical(): bool
    {
        return $this->canonical !== null;
    }

    public function hasMultipleCanonicals(): bool
    {
        return count($this->canonicalUrls) > 1;
    }

    public function hasPageUrl(): bool
    {
        return $this->pageUrl !== null && $this->pageUrl !== '';
    }
}

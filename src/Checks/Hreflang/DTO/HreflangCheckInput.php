<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\DTO;

final readonly class HreflangCheckInput
{
    /** @var array<int, array{hreflang: string, href: string}> */
    public array $entries;

    public function __construct(array $entries = [])
    {
        $this->entries = $entries;
    }

    public function hasEntries(): bool
    {
        return $this->entries !== [];
    }

    public function entryCount(): int
    {
        return count($this->entries);
    }

    /** @return array<int, array{hreflang: string, href: string}> */
    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getHreflang(int $index): ?string
    {
        return isset($this->entries[$index]['hreflang']) ? (string) $this->entries[$index]['hreflang'] : null;
    }

    public function getHref(int $index): ?string
    {
        return isset($this->entries[$index]['href']) ? (string) $this->entries[$index]['href'] : null;
    }

    public function isHreflangEmpty(int $index): bool
    {
        $v = $this->getHreflang($index);
        return $v !== null && trim($v) === '';
    }

    public function isHrefEmpty(int $index): bool
    {
        $v = $this->getHref($index);
        return $v !== null && trim($v) === '';
    }
}

<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\DTO;

final readonly class HreflangEntryReport
{
    public function __construct(
        public int $index,
        public string $hreflang,
        public string $href,
        public string $status,
        public string $message = '',
    ) {}
}

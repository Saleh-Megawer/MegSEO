<?php

declare(strict_types=1);

namespace MegSEO\DTO;

final readonly class CheckReference
{
    public function __construct(
        public string $id,
        public string $label,
        public ?string $version = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\DTO;

final readonly class TwitterCardPropertyReport
{
    public function __construct(
        public string $property,
        public string $status,
        public ?string $value = null,
        public string $message = '',
    ) {}
}

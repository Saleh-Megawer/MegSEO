<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph\DTO;

final readonly class OpenGraphPropertyReport
{
    public function __construct(
        public string $property,
        public string $status, // 'missing', 'empty', 'valid', 'invalid'
        public ?string $value = null,
        public string $message = '',
    ) {}
}

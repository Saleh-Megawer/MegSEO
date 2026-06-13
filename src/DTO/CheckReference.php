<?php

declare(strict_types=1);

namespace MegSEO\DTO;

/**
 * Stable identity record for a registered check.
 *
 * ## Stability Contract
 *
 * The `id` field is a public contract. Once published, it MUST remain
 * unchanged across releases. Consumers depend on stable identifiers
 * for reporting, filtering, debugging, and dashboard integration.
 *
 * The `id` MUST be unique within a single registry instance.
 * The `version` field is optional and may track check contract versions.
 */
final readonly class CheckReference
{
    public function __construct(
        public string $id,
        public string $label,
        public ?string $version = null,
    ) {}
}

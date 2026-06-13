<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

/**
 * Downstream-safe result adapter.
 *
 * Implementations promise a stable {@see toArray()} representation
 * suitable for serialization, logging, API responses, and
 * dashboard integration.
 *
 * The array shape is considered part of the public contract and
 * must remain backward-compatible once released.
 */
interface ArrayableResult
{
    /**
     * Returns a stable array representation of the result.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}

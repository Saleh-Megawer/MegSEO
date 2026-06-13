<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

use MegSEO\DTO\AnalysisContext;

/**
 * Factory contract for creating {@see AnalysisContext} instances.
 *
 * Provides a simplified interface for callers who want to create
 * analysis contexts without manually constructing the DTO.
 */
interface ContextFactory
{
    /**
     * Creates an AnalysisContext with the given subject and metadata.
     *
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $options
     */
    public function create(
        mixed $subject,
        array $attributes = [],
        array $options = [],
        ?string $requestId = null,
    ): AnalysisContext;
}

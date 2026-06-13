<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

/**
 * Registration contract for the check extension mechanism.
 *
 * Implementations maintain an ordered collection of {@see Check}
 * instances and provide enumeration for pipeline execution.
 *
 * ## Duplicate Handling
 *
 * Registries MUST treat the check identifier as unique within a
 * single instance. Registering a check with an identifier that
 * already exists MUST throw a
 * {@see \MegSEO\Exceptions\DuplicateCheckIdentifierException}. This
 * protects against accidental double-registration and ensures each
 * identifier maps to exactly one check implementation.
 *
 * ## Ordering
 *
 * Checks are returned in registration order by {@see all()}.
 * This ordering is deterministic and preserved across calls.
 */
interface RegistersChecks
{
    /**
     * Registers a check. If the check's identifier already exists,
     * a {@see \MegSEO\Exceptions\DuplicateCheckIdentifierException}
     * is thrown.
     */
    public function register(Check $check): void;

    /**
     * Returns all registered checks in deterministic registration order.
     *
     * @return array<int, Check>
     */
    public function all(): array;

    /**
     * Returns the number of registered checks.
     */
    public function count(): int;

    /**
     * Returns true if a check with the given identifier is registered.
     */
    public function has(string $id): bool;
}

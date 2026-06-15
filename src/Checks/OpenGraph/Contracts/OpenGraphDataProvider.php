<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph\Contracts;

interface OpenGraphDataProvider
{
    public function hasProperty(string $key): bool;

    public function getProperty(string $key): ?string;

    /** @return array<string, string|string[]> */
    public function all(): array;
}

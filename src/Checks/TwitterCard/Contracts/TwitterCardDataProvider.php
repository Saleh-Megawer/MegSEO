<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\Contracts;

interface TwitterCardDataProvider
{
    public function hasProperty(string $key): bool;
    public function getProperty(string $key): ?string;
    public function all(): array;
}

<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\DTO;

final readonly class TwitterCardCheckInput
{
    public array $allProperties;

    public function __construct(array $allProperties = [])
    {
        $this->allProperties = $allProperties;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->allProperties);
    }

    public function get(string $key): ?string
    {
        if (! $this->has($key)) return null;
        $val = $this->allProperties[$key];
        return is_array($val) ? (string) ($val[0] ?? '') : (string) $val;
    }

    public function isEmpty(string $key): bool
    {
        return $this->has($key) && ($this->get($key) === '' || trim($this->get($key) ?? '') === '');
    }

    /** @return string[] */
    public function getArray(string $key): array
    {
        if (! $this->has($key)) return [];
        $val = $this->allProperties[$key];
        return is_array($val) ? array_map('strval', $val) : [(string) $val];
    }
}

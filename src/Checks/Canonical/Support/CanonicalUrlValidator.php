<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Support;

final readonly class CanonicalUrlValidator
{
    public function isValid(string $url): bool
    {
        $parts = parse_url($url);
        if ($parts === false) {
            return false;
        }

        if (! isset($parts['scheme']) || ! in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return false;
        }

        if (! isset($parts['host']) || $parts['host'] === '') {
            return false;
        }

        return true;
    }

    public function isRelative(string $url): bool
    {
        $parts = parse_url($url);
        if ($parts === false) {
            return true;
        }

        return ! isset($parts['scheme']) || $parts['scheme'] === '';
    }
}

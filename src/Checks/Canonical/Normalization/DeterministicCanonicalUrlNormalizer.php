<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Normalization;

use MegSEO\Checks\Canonical\Contracts\CanonicalUrlNormalizer;
use MegSEO\Checks\Canonical\DTO\CanonicalUrlNormalizationResult;

final class DeterministicCanonicalUrlNormalizer implements CanonicalUrlNormalizer
{
    public function normalize(?string $canonicalUrl, ?string $pageUrl = null): CanonicalUrlNormalizationResult
    {
        $flags = [];

        if ($canonicalUrl === null || $canonicalUrl === '') {
            return new CanonicalUrlNormalizationResult($canonicalUrl, $canonicalUrl, $pageUrl, $pageUrl, ['action' => 'skip-empty']);
        }

        $normalizedCanonical = $this->normalizeUrl($canonicalUrl, $flags);
        $pageFlags = [];
        $normalizedPage = $pageUrl !== null ? $this->normalizeUrl($pageUrl, $pageFlags) : null;

        return new CanonicalUrlNormalizationResult(
            rawCanonical: $canonicalUrl,
            normalizedCanonical: $normalizedCanonical,
            rawPageUrl: $pageUrl,
            normalizedPageUrl: $normalizedPage,
            flags: $flags,
        );
    }

    public function normalizeUrl(string $url, array &$flags): string
    {
        $parts = parse_url($url);
        if ($parts === false) {
            $flags['parse_failed'] = true;
            return '';
        }

        $scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : '';
        $host = isset($parts['host']) ? strtolower($parts['host']) : '';
        $port = isset($parts['port']) ? (int) $parts['port'] : null;
        $path = isset($parts['path']) ? $parts['path'] : '/';
        $query = isset($parts['query']) ? $parts['query'] : '';

        // Strip trailing slash (unless root)
        if ($path !== '/') {
            $before = $path;
            $path = rtrim($path, '/');
            if ($before !== $path) {
                $flags['trailing_slash_stripped'] = true;
            }
        }

        // Strip default ports
        if ($port === 80 && $scheme === 'http') {
            $port = null;
            $flags['port_stripped'] = true;
        } elseif ($port === 443 && $scheme === 'https') {
            $port = null;
            $flags['port_stripped'] = true;
        }

        // Sort query params
        if ($query !== '') {
            $before = $query;
            parse_str($query, $params);
            ksort($params);
            $query = http_build_query($params);
            if ($before !== $query) {
                $flags['query_sorted'] = true;
            }
        }

        $normalized = "{$scheme}://{$host}";
        if ($port !== null) {
            $normalized .= ":{$port}";
        }
        $normalized .= $path;
        if ($query !== '') {
            $normalized .= "?{$query}";
        }

        return $normalized;
    }
}

# Contract: Canonical URL Normalization

## Purpose

Define the deterministic URL normalization expectations used by Canonical Check.

## Behavioral Guarantees

- identical inputs produce identical normalized outputs
- normalization happens before rule evaluation
- normalization handles scheme, hostname, port, path, and query string
- normalization supports Unicode and IDN characters

## Normalization Rules

- Lowercase the scheme (e.g., `HTTPS` → `https`)
- Lowercase the hostname (e.g., `EXAMPLE.COM` → `example.com`)
- Strip default ports (80 for http, 443 for https)
- Remove trailing slashes from paths (e.g., `/page/` → `/page`)
- Sort query parameters by key for deterministic comparison
- Decode safe percent-encoded characters

## Compatibility Rules

- normalization behavior changes that affect observable findings must be documented
- normalization metadata should remain additive

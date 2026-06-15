# Quickstart: Canonical Check

## Goal

Use Canonical Check as the first technical (URL-based) MegSEO feature, following the established Title Check and Meta Description Check patterns.

## 1. Supply canonical input

Provide canonical URL data through the existing MegSEO context:

- the raw canonical URL
- optional multiple canonical URLs
- the page URL for comparison

## 2. Run analysis

```php
$result = MegSEO::analyze($context);
```

## 3. Inspect outcomes

- Issues for missing, empty, invalid, or multiple canonicals
- Warnings for relative canonical URLs
- Suggestions for cross-domain or non-self-referencing canonicals
- Score contribution rationale
- Stable metadata and identifiers

## 4. URL normalization

Canonical URLs are normalized before comparison:

- Lowercase scheme and hostname
- Strip default ports (80, 443)
- Remove trailing slashes
- Sort query parameters
- Decode safe percent-encoding

## 5. Follow the established pattern

Canonical Check follows the same architectural shape:

- Feature-scoped module under `src/Checks/Canonical/`
- Deterministic URL normalization
- Composable rule objects (7 rules)
- Explicit rationale and confidence
- Stable identifier (`seo.canonical`) and metadata
- Thin Laravel adapter for registration

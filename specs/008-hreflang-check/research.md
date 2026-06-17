# Research: Hreflang Check

## Decision 1: BCP 47 language code validation

**Decision**: Validate hreflang codes against BCP 47 pattern: `/^[a-z]{2}(-[A-Z]{2})?$/`, plus special value `x-default`.

**Rationale**: BCP 47 is the RFC standard for language tags. Common codes like `en`, `en-US`, `ar`, `zh-Hans`, `es-MX` all match.

## Decision 2: Array-of-entries input model

**Decision**: Input is an array of associative arrays, each with `hreflang` and `href` keys.

**Rationale**: Hreflang annotations are naturally a set of entries, not key-value pairs. This input model differs from OG/Twitter's key-value structure.

## Decision 3: Reuse CanonicalUrlValidator

**Decision**: Import `MegSEO\Checks\Canonical\Support\CanonicalUrlValidator` for href URL validation.

**Rationale**: Same URL requirements as canonical/OG/Twitter. Cross-feature reuse is proven.

## Decision 4: Separate per-entry and cross-entry rules

**Decision**: Per-entry rules (empty, language, URL) evaluate individual entries. Cross-entry rules (x-default, self-referencing, conflicts) evaluate the set as a whole.

**Rationale**: Clear separation of concerns. Per-entry rules can short-circuit (e.g., empty skips URL validation).

## Decision 5: x-default is a suggestion, not an issue

**Decision**: Missing x-default produces a suggestion, not an issue.

**Rationale**: x-default is recommended but not required. Some sites use country-specific fallbacks instead.

## Decision 6: Follow the established pattern

**Decision**: DTOs → rules → orchestrator → score builder → thin adapter. Consistent with all previous checks.

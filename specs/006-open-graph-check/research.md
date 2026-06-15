# Research: Open Graph Check

## Decision 1: Reuse CanonicalUrlValidator for image URL validation

**Decision**: Import and use `MegSEO\Checks\Canonical\Support\CanonicalUrlValidator` for og:image URL validation.

**Rationale**: Image URL validation is identical to canonical URL validation — scheme must be http/https, host must be present, relative URLs flagged separately. This is the first cross-feature reuse of a support class, validating the architecture's composition model.

**Alternatives considered**: Duplicate URL validator in OpenGraph feature — rejected because cross-feature reuse is a design goal.

## Decision 2: Six rule evaluators for Open Graph domain

**Decision**: Presence rules (3: title, description, image), emptiness rule (1: combined), image quality rule (1: URL validity), conflict rule (1: multiple values).

**Rationale**: OG is a structured data check — each OG property is an independent concern. Six rules keep each focused and testable.

**Alternatives considered**: Combined presence rules — rejected because per-property messages are more actionable.

## Decision 3: No text normalization

**Decision**: OG values are opaque strings — compared for presence and emptiness only, not analyzed for content quality.

**Rationale**: Unlike title/description checks, OG values are metadata labels, not content to be optimized. Length and keyword analysis are out of scope.

**Alternatives considered**: Content analysis — rejected because OG tags are platform-specific, not content-quality markers.

## Decision 4: Structured array input

**Decision**: OG data arrives as an associative array (`['og:title' => '...', 'og:description' => '...', ...]`).

**Rationale**: OG data is naturally key-value. Arrays allow sparse input (some properties missing) and multiple values for conflict detection.

**Alternatives considered**: Separate context attributes per property — rejected because arrays better represent the OG object model.

## Decision 5: Follow the established architectural pattern

**Decision**: DTOs → rules → orchestrator → score builder → thin Laravel adapter.

**Rationale**: Proven across 3 features. Cross-feature reuse of `CanonicalUrlValidator` demonstrates architecture maturity.

**Alternatives considered**: Different architecture — rejected because consistency is a requirement.

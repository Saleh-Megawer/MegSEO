# Research: Canonical Check

## Decision 1: Use deterministic URL normalization

**Decision**: Normalize canonical and page URLs using standardized URL normalization — lowercase scheme and hostname, strip default ports (80/443), remove trailing slashes, sort query parameters, decode safe percent-encoded characters.

**Rationale**: Deterministic comparison requires canonical URL representation. Industry-standard normalization ensures consistent behavior across all URL variants.

**Alternatives considered**: Raw string comparison — rejected because `https://example.com`, `https://example.com/`, `https://EXAMPLE.COM`, and `https://example.com:443` should be treated as equivalent.

## Decision 2: Compose from 7 rule evaluators

**Decision**: Seven small rule objects for presence, validity, multiplicity, self-referencing, relativity, and cross-domain concerns.

**Rationale**: Canonical analysis has more dimensions than text checks. Composition keeps each rule focused and testable while maintaining the established pattern.

**Alternatives considered**: Fewer combined rules — rejected because mixing concerns (e.g., validity + relativity) would reduce clarity and testability.

## Decision 3: Treat cross-domain canonicals as suggestions

**Decision**: Cross-domain canonicals produce suggestions, not issues.

**Rationale**: Cross-domain canonicals are a legitimate pattern for syndicated content, canonicalized third-party articles, and multi-site architectures. Flagging as an issue would generate false positives.

**Alternatives considered**: Issue for cross-domain — rejected because it would penalize valid use cases.

## Decision 4: Treat relative URLs as warnings

**Decision**: Relative canonical URLs produce warnings recommending absolute URLs.

**Rationale**: While the HTML spec technically permits relative canonical URLs, Google and major search engines strongly recommend absolute URLs. Warnings balance correctness with pragmatism.

**Alternatives considered**: Issue for relative — rejected because relative canonicals technically parse correctly in their document context.

## Decision 5: Supply page URL via analysis context

**Decision**: The page URL required for self-referencing and cross-domain checks is supplied through the analysis context alongside the canonical URL.

**Rationale**: The check needs both the canonical URL and the page URL for comparison. The analysis context already supports this data flow.

**Alternatives considered**: Deriving page URL from input — rejected because the context provides cleaner separation.

## Decision 6: Follow the established architectural pattern

**Decision**: Mirror Title Check / Meta Description Check architecture exactly — DTOs, normalization, composable rules, score builder, thin Laravel adapter.

**Rationale**: Proven pattern with two successful implementations. Consistency across features is a requirement.

**Alternatives considered**: Different architecture for URL-based checks — rejected because the feature pattern is explicitly designed to be reusable.

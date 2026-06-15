# Research: Twitter Card Check

## Decision 1: Mirror Open Graph Check architecture

**Decision**: Use the same rule composition pattern as Open Graph Check — presence, emptiness, image URL, conflicts — with one addition: card type validation.

**Rationale**: OG Check validated the social metadata pattern. Card type validation is the only unique domain concern.

## Decision 2: Reuse CanonicalUrlValidator

**Decision**: Import `MegSEO\Checks\Canonical\Support\CanonicalUrlValidator` for twitter:image URL validation.

**Rationale**: Same URL validation logic applies. Cross-feature reuse is proven across OG Check.

## Decision 3: Validate card types

**Decision**: Supported types: `summary`, `summary_large_image`, `app`, `player`. Invalid types produce warnings.

**Rationale**: Twitter specifies these four card types. Invalid types cause Twitter to fall back to a generic card.

## Decision 4: Inherit empty-suppresses-missing and no-duplicate-conflicts

**Decision**: Same behavioral rules as Open Graph Check for consistency.

**Rationale**: Twitter Card properties follow the same logical structure as OG properties.

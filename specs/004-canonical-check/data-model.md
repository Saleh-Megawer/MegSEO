# Data Model: Canonical Check

## CanonicalCheckInput

- **Purpose**: Immutable feature input for one canonical analysis run
- **Key fields**:
  - `canonical`: raw canonical URL value or missing state
  - `canonicals`: array of canonical values (for multiple canonical detection)
  - `pageUrl`: the page URL for comparison
  - `attributes`: additional feature-safe metadata
- **Relationships**:
  - consumed by `CanonicalCheck`
  - transformed into `CanonicalUrlNormalizationResult`
- **Validation rules**:
  - must support missing canonical data safely
  - `canonicals` array enables multiple canonical detection

## CanonicalUrlNormalizationResult

- **Purpose**: Normalized URL representations used by all rules
- **Key fields**:
  - `rawCanonical`: raw canonical URL
  - `normalizedCanonical`: normalized canonical URL
  - `rawPageUrl`: raw page URL
  - `normalizedPageUrl`: normalized page URL
  - `normalizationFlags`: transformations applied
- **Relationships**:
  - produced by `DeterministicCanonicalUrlNormalizer`
  - consumed by all rule evaluators
- **Validation rules**:
  - identical inputs produce identical normalized outputs
  - handles Unicode and IDN characters

## CanonicalUrlMatchReport

- **Purpose**: Result of comparing canonical URL against page URL
- **Key fields**:
  - `isSelfReferencing`: whether canonical matches page URL
  - `isCrossDomain`: whether canonical points to different domain
  - `isRelative`: whether canonical is a relative URL
  - `matchDetails`: human-readable explanation
- **Relationships**:
  - produced by normalization comparison
  - consumed by self-referencing, cross-domain, and relative URL rules

## CanonicalCheckFinding

- **Purpose**: Feature-specific issue, warning, or suggestion output
- **Key fields**: severity, message, whyItMatters, howToImprove, confidence, metadata
- **Relationships**: produced by rule evaluators, aggregated into CheckOutcome

## CanonicalScoreContribution

- **Purpose**: Structured score effect
- **Key fields**: value, rationale, sourceRule, metadata
- **Relationships**: assembled by `CanonicalScoreContributionBuilder`

## CanonicalCheckMetadata

- **Purpose**: Stable metadata for dashboards
- **Key fields**:
  - `checkIdentifier` (`seo.canonical`)
  - `ruleIdentifiers`
  - `isSelfReferencing`
  - `isCrossDomain`
  - `isRelative`
  - `multipleCanonicalsDetected`
  - `normalizationApplied`
- **Relationships**: attached to CheckOutcome metadata

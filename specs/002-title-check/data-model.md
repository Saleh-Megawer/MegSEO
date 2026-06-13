# Data Model: Title Check

## TitleCheckInput

- **Purpose**: Immutable feature input for one title analysis run
- **Key fields**:
  - `title`: raw title value or missing state
  - `focusKeyword`: optional focus keyword input
  - `duplicateSupportData`: optional duplicate-title support context
  - `attributes`: additional feature-safe metadata
- **Relationships**:
  - consumed by `TitleCheck`
  - transformed into `TitleNormalizationResult`
- **Validation rules**:
  - must support missing title data safely
  - must not require external services

## TitleNormalizationResult

- **Purpose**: Canonical normalized representation used by all rules
- **Key fields**:
  - `rawTitle`
  - `normalizedTitle`
  - `normalizedFocusKeyword`
  - `normalizationFlags`
- **Relationships**:
  - produced by `DeterministicTitleNormalizer`
  - consumed by all rule evaluators
- **Validation rules**:
  - identical inputs must produce identical normalized outputs
  - must support Arabic and Unicode safely

## TitleDuplicateMatch

- **Purpose**: Optional structured duplicate-title support record
- **Key fields**:
  - `matchedTitle`
  - `matchedReference`
  - `matchReason`
- **Relationships**:
  - derived from `duplicateSupportData`
  - may influence `TitleCheckFinding` and metadata

## TitleCheckFinding

- **Purpose**: Feature-specific issue, warning, or suggestion output
- **Key fields**:
  - `severity`
  - `message`
  - `whyItMatters`
  - `howToImprove`
  - `confidence`
  - `metadata`
- **Relationships**:
  - produced by rule evaluators
  - aggregated into the core check outcome
- **Validation rules**:
  - should remain within title scope
  - severe problems map to issues, moderate concerns to warnings, improvements to suggestions

## TitleScoreContribution

- **Purpose**: Structured score effect emitted by the feature
- **Key fields**:
  - `value`
  - `rationale`
  - `sourceRule`
  - `metadata`
- **Relationships**:
  - assembled by `TitleScoreContributionBuilder`
  - included in the feature’s core outcome

## TitleCheckMetadata

- **Purpose**: Stable metadata for dashboards, reporting, and developer inspection
- **Key fields**:
  - `checkIdentifier`
  - `ruleIdentifiers`
  - `normalizedLength`
  - `duplicateSupportUsed`
  - `focusKeywordSupplied`
- **Relationships**:
  - attached to feature output and findings
- **Validation rules**:
  - identifiers must be stable once public
  - metadata should be additive over time

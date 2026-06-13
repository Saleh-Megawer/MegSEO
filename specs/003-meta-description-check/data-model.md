# Data Model: Meta Description Check

## MetaDescriptionCheckInput

- **Purpose**: Immutable feature input for one meta description analysis run
- **Key fields**:
  - `description`: raw description value or missing state
  - `focusKeyword`: optional focus keyword input
  - `duplicateSupportData`: optional duplicate-description support context
  - `attributes`: additional feature-safe metadata
- **Relationships**:
  - consumed by `MetaDescriptionCheck`
  - transformed into `MetaDescriptionNormalizationResult`
- **Validation rules**:
  - must support missing description data safely
  - must not require external services

## MetaDescriptionNormalizationResult

- **Purpose**: Canonical normalized representation used by all rules
- **Key fields**:
  - `rawDescription`
  - `normalizedDescription`
  - `normalizedFocusKeyword`
  - `normalizationFlags`
- **Relationships**:
  - produced by `DeterministicMetaDescriptionNormalizer`
  - consumed by all rule evaluators
- **Validation rules**:
  - identical inputs must produce identical normalized outputs
  - must support Arabic and Unicode safely

## MetaDescriptionDuplicateMatch

- **Purpose**: Optional structured duplicate-description support record
- **Key fields**:
  - `matchedDescription`
  - `matchedReference`
  - `matchReason`
- **Relationships**:
  - derived from `duplicateSupportData`
  - may influence findings and metadata

## MetaDescriptionCheckFinding

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
  - severe problems map to issues, moderate concerns to warnings, improvements to suggestions

## MetaDescriptionScoreContribution

- **Purpose**: Structured score effect emitted by the feature
- **Key fields**:
  - `value`
  - `rationale`
  - `sourceRule`
  - `metadata`
- **Relationships**:
  - assembled by `MetaDescriptionScoreContributionBuilder`
  - included in the `ScoreSummary` within `CheckOutcome`

## MetaDescriptionCheckMetadata

- **Purpose**: Stable metadata for dashboards, reporting, and developer inspection
- **Key fields**:
  - `checkIdentifier` (`seo.meta_description`)
  - `ruleIdentifiers`
  - `normalizedLength`
  - `duplicateSupportUsed`
  - `focusKeywordSupplied`
- **Relationships**:
  - attached to feature output and findings
- **Validation rules**:
  - identifiers must be stable once public
  - metadata should be additive over time

# Data Model: Open Graph Check

## OpenGraphCheckInput

- **Purpose**: Immutable input for OG analysis
- **Key fields**:
  - `ogTitle`: og:title value or null
  - `ogDescription`: og:description value or null
  - `ogImage`: og:image value or null
  - `allProperties`: full associative array of OG properties
  - `ogImages`: array of all og:image values (for conflict detection)
- **Relationships**: Consumed by `OpenGraphCheck`, passed to rule evaluators

## OpenGraphPropertyReport

- **Purpose**: Per-property analysis result
- **Key fields**: property name, value, status (missing/empty/valid/invalid/relative), message
- **Relationships**: Produced by rule evaluators, aggregated into findings

## OpenGraphCheckMetadata

- **Purpose**: Stable dashboard metadata
- **Key fields**:
  - `checkIdentifier` (`seo.open_graph`)
  - `ruleIdentifiers`
  - `ogTitleProvided`, `ogDescriptionProvided`, `ogImageProvided`
  - `validImageUrl`, `relativeImageUrl`
  - `conflictingValuesDetected`
- **Relationships**: Attached to CheckOutcome

## OpenGraphScoreContribution

- **Purpose**: Score impact with rationale
- **Key fields**: value, rationale, sourceRule
- **Relationships**: Assembled by `OpenGraphScoreContributionBuilder`

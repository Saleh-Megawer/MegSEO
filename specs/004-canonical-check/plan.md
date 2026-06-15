# Implementation Plan: Canonical Check

**Branch**: `004-canonical-check` | **Date**: 2026-06-13 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/004-canonical-check/spec.md`

## Summary

Implement the third MegSEO feature as a Canonical Check that plugs into the validated core analysis engine, evaluates canonical tags for presence, validity, and quality, and returns deterministic issues, warnings, suggestions, score contributions, and stable metadata. This is the first technical (URL-based) SEO check, extending the established Title Check / Meta Description Check reference pattern into the technical SEO domain.

## Technical Context

**Language/Version**: PHP 8.2+  
**Primary Dependencies**: Existing MegSEO core analysis engine contracts, Composer package structure, Laravel integration layer, PHP URL parsing (`parse_url`), Unicode string handling  
**Storage**: N/A  
**Testing**: Pest for unit, contract, and Laravel integration coverage  
**Target Platform**: Composer-distributed Laravel package  
**Constraints**: No HTTP requests, no external APIs, no crawling, deterministic results for identical inputs, stable public contracts, URL normalization handles Unicode/IDN  
**Scale/Scope**: One production-ready technical SEO feature module

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Architecture Before Features**: Pass. Follows approved specification and extends validated core.
- **Feature-Driven Development**: Pass. Scoped to dedicated Canonical feature module under `src/Checks/`.
- **Generic Core, Domain-Specific Features**: Pass. All URL/canonical logic inside feature; core remains generic.
- **Open for Extension**: Pass. Builds on core contracts, composition, configurable thresholds.
- **Quality, Documentation, and Stability**: Pass. Includes unit tests, edge-case coverage, Laravel integration.
- **Actionable, Educational Output Philosophy**: Pass. Findings explain what, why, and how to improve.
- **Arabic and Future Language Support**: Pass. IDN and Unicode URLs handled via normalization.
- **Laravel-First Developer Experience**: Pass. Integrates through existing package surface.
- **Determinism and Human Override**: Pass. Deterministic URL normalization and stable identifiers.

## Project Structure

### Documentation

```text
specs/004-canonical-check/
├── plan.md
├── research.md
├── data-model.md
├── quickstart.md
├── contracts/
│   ├── canonical-check-api.md
│   ├── canonical-finding-contract.md
│   ├── canonical-url-normalization-contract.md
│   └── canonical-page-url-contract.md
└── tasks.md
```

### Source Code

```text
src/
├── Checks/
│   └── Canonical/
│       ├── Contracts/
│       │   ├── CanonicalUrlNormalizer.php
│       │   └── SupportsPageUrl.php
│       ├── DTO/
│       │   ├── CanonicalCheckInput.php
│       │   ├── CanonicalCheckMetadata.php
│       │   ├── CanonicalUrlNormalizationResult.php
│       │   └── CanonicalUrlMatchReport.php
│       ├── Normalization/
│       │   └── DeterministicCanonicalUrlNormalizer.php
│       ├── Rules/
│       │   ├── DetectMissingCanonical.php
│       │   ├── DetectEmptyCanonical.php
│       │   ├── DetectInvalidCanonicalUrl.php
│       │   ├── DetectMultipleCanonicals.php
│       │   ├── EvaluateSelfReferencingCanonical.php
│       │   ├── EvaluateRelativeCanonicalUrl.php
│       │   └── EvaluateCrossDomainCanonical.php
│       ├── Scoring/
│       │   └── CanonicalScoreContributionBuilder.php
│       ├── Support/
│       │   └── CanonicalUrlValidator.php
│       └── CanonicalCheck.php
├── Laravel/
│   └── Support/
│       └── CanonicalCheckRegistration.php
└── Contracts/
    └── Check.php

tests/
├── Unit/
│   └── Checks/
│       └── Canonical/
├── Contract/
│   └── Checks/
│       └── Canonical/
└── Integration/
    ├── Checks/
    │   └── Canonical/
    └── Laravel/
```

**Structure Decision**: Use `src/Checks/Canonical/` following the established convention. Seven rule evaluators mapped to the unique canonical domain (missing, empty, invalid, multiple, self-referencing, relative, cross-domain). Uses `CanonicalUrlNormalizer` and `CanonicalUrlValidator` as domain-specific support helpers.

## Phase 0: Research

### Key Decisions

- Use deterministic URL normalization (lowercase scheme/host, strip default ports, remove trailing slashes, sort query params) matching industry standards.
- Model Canonical Check as composition of 7 small rule evaluators.
- Treat cross-domain canonicals as suggestions (may be intentional).
- Treat relative URLs as warnings (recommend absolute).
- Keep self-referencing canonical detection logic purely comparative — no HTTP requests.
- Reuse the same `CheckOutcome` DTO for results.

### Rationale

- URL normalization is the canonical equivalent of text normalization in Title/Description checks.
- Seven rules reflect the richer domain of canonical validation (presence, validity, multiplicity, self-reference, relativity, cross-domain).
- Self-referencing detection requires comparing a normalized canonical URL against a supplied page URL.

### Alternatives Considered

- **HTTP validation of canonical URLs**: Rejected — out of scope, no external requests.
- **Fewer rules (monolithic)**: Rejected — contradicts the established composition pattern.

## Phase 1: Design

### Core Components

- `CanonicalCheck`: Orchestrator implementing `Check`. Stable identifier: `seo.canonical`.
- `DeterministicCanonicalUrlNormalizer`: URL normalization (scheme, host, path, query).
- `CanonicalUrlValidator`: Validates URL structure (scheme, host, format).
- Rule evaluators (7 rules):
  - `DetectMissingCanonical`
  - `DetectEmptyCanonical`
  - `DetectInvalidCanonicalUrl`
  - `DetectMultipleCanonicals`
  - `EvaluateSelfReferencingCanonical`
  - `EvaluateRelativeCanonicalUrl`
  - `EvaluateCrossDomainCanonical`
- `CanonicalScoreContributionBuilder`: Scoring with rationale.
- `CanonicalCheckRegistration`: Thin Laravel adapter.

### Scoring Philosophy

| Finding | Deduction |
|---------|-----------|
| Missing canonical | -40 |
| Empty canonical | -35 |
| Invalid URL | -30 |
| Multiple canonicals | -25 |
| Relative URL | -15 |
| Cross-domain warning | -10 |
| Not self-referencing | -5 |
| Clean canonical | 0 (score: 100) |

### Pipeline Execution

1. Normalize canonical URL and page URL
2. Evaluate presence/validity rules (missing, empty, invalid, multiple)
3. Evaluate quality rules (relative, self-referencing, cross-domain)
4. Assemble score and metadata into `CheckOutcome`

### Laravel Integration

- `CanonicalCheckRegistration`: Thin adapter resolving config values for URL normalization preferences.
- Core classes remain framework-agnostic.

### Testing Strategy

- **Unit**: URL normalization, URL validation, 7 rule evaluators, score builder.
- **Contract**: Stable identifier, metadata consistency, deterministic outputs.
- **Integration**: Full check through engine, multiple canonical scenarios, Unicode/IDN URLs, deterministic repeats.
- **Laravel**: Registration flow, config consumption.

## Phase 2: Implementation Strategy

1. Build feature shell under `src/Checks/Canonical/`.
2. Implement URL normalization and validation first (blocking dependency).
3. Implement presence/validity rules before quality rules.
4. Implement scoring and metadata.
5. Wire into Laravel integration.
6. Complete test coverage.
7. Update dogfooding example.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| None | N/A | N/A |

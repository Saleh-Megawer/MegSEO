# Implementation Plan: Meta Description Check

**Branch**: `003-meta-description-check` | **Date**: 2026-06-13 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/003-meta-description-check/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command.

## Summary

Implement the second MegSEO feature as a Meta Description Check that plugs into the validated core analysis engine, evaluates normalized meta descriptions for presence and quality, and returns deterministic issues, warnings, suggestions, score contributions, confidence signals, and stable metadata. The design follows the established Title Check reference pattern while remaining fully within meta description scope.

## Technical Context

**Language/Version**: PHP 8.2+  
**Primary Dependencies**: Existing MegSEO core analysis engine contracts, Composer package structure, Laravel integration layer, PHP Unicode string handling, project configuration support for tunable description thresholds  
**Storage**: N/A for the check itself; optional duplicate-description support data is supplied through the analysis context  
**Testing**: PHPUnit or Pest for unit, contract, and Laravel integration coverage  
**Target Platform**: Composer-distributed Laravel package with framework-agnostic core check behavior  
**Project Type**: Library / Laravel package feature module  
**Performance Goals**: Description analysis should add only minimal overhead beyond normalization, rule evaluation, and result assembly for a single check run  
**Constraints**: No external APIs, no scraping, no AI generation, no behavior outside meta description scope, deterministic results for identical inputs, stable public contracts, Arabic and Unicode support as first-class behavior, configurable thresholds whenever practical  
**Scale/Scope**: One production-ready feature module that follows the validated Title Check design pattern

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Architecture Before Features**: Pass. Follows approved specification and extends validated core.
- **Feature-Driven Development**: Pass. Scoped to dedicated MetaDescription feature module.
- **Generic Core, Domain-Specific Features**: Pass. All SEO logic inside MetaDescription feature; core remains generic.
- **Open for Extension**: Pass. Builds on core contracts, composition, and configurable thresholds.
- **Quality, Documentation, and Stability**: Pass. Includes unit tests, edge-case coverage, Laravel integration, documentation.
- **Actionable, Educational Output Philosophy**: Pass. Findings designed around issues, warnings, suggestions, rationale.
- **Arabic and Future Language Support**: Pass. Arabic and Unicode handling are explicit design concerns.
- **Laravel-First Developer Experience**: Pass. Integrates with existing package surface consistently.
- **Determinism and Human Override**: Pass. Deterministic normalization, stable identifiers, explicit rationale/confidence.

## Project Structure

### Documentation (this feature)

```text
specs/003-meta-description-check/
├── plan.md
├── research.md
├── data-model.md
├── quickstart.md
├── contracts/
│   ├── meta-check-api.md
│   ├── meta-check-finding-contract.md
│   ├── meta-normalization-contract.md
│   └── duplicate-description-contract.md
└── tasks.md
```

### Source Code (repository root)

```text
src/
├── Checks/
│   └── MetaDescription/
│       ├── Contracts/
│       │   ├── SupportsDuplicateDescriptions.php
│       │   ├── SupportsFocusKeyword.php
│       │   └── MetaDescriptionNormalizer.php
│       ├── DTO/
│       │   ├── MetaDescriptionCheckInput.php
│       │   ├── MetaDescriptionCheckMetadata.php
│       │   ├── MetaDescriptionDuplicateMatch.php
│       │   └── MetaDescriptionNormalizationResult.php
│       ├── Normalization/
│       │   └── DeterministicMetaDescriptionNormalizer.php
│       ├── Rules/
│       │   ├── DetectMissingMetaDescription.php
│       │   ├── DetectEmptyMetaDescription.php
│       │   ├── DetectSeparatorOnlyMetaDescription.php
│       │   ├── EvaluateMetaDescriptionLength.php
│       │   ├── EvaluateFocusKeywordPresence.php
│       │   └── EvaluateDuplicateMetaDescriptionSupport.php
│       ├── Scoring/
│       │   └── MetaDescriptionScoreContributionBuilder.php
│       ├── Support/
│       │   ├── MetaDescriptionCharacterClassifier.php
│       │   └── MetaDescriptionLengthPolicy.php
│       └── MetaDescriptionCheck.php
├── Laravel/
│   └── Support/
│       └── MetaDescriptionCheckRegistration.php
└── Contracts/
    └── Check.php

config/
└── megseo.php

tests/
├── Unit/
│   └── Checks/
│       └── MetaDescription/
├── Contract/
│   └── Checks/
│       └── MetaDescription/
└── Integration/
    ├── Checks/
    │   └── MetaDescription/
    └── Laravel/
```

**Structure Decision**: Use the `src/Checks/MetaDescription/` convention established by Title Check. Supporting contracts and DTOs remain inside the feature boundary. Laravel wiring stays minimal via `MetaDescriptionCheckRegistration`.

## Phase 0: Research Summary

### Key Decisions

- Use the same deterministic normalization strategy as Title Check — the `DeterministicTitleNormalizer` pattern is adapted for meta description text.
- Model the Meta Description Check as a composition of small rule evaluators mirroring Title Check's rule pattern.
- Keep recommended meta description length thresholds configurable (defaults: 120–160 characters, short threshold at 80, long threshold at 170).
- Treat duplicate-description support as optional contextual capability with safe degradation.
- Use Unicode-aware and Arabic-safe string handling throughout.
- Emit stable identifiers, metadata, rationale, and confidence matching the Title Check output pattern.
- Reuse the same `CheckOutcome` DTO for results — no feature-specific result wrapper.

### Rationale

- Title Check already validated the deterministic normalization → rule evaluation → score assembly pipeline.
- Mirroring the pattern reduces implementation risk and ensures consistent developer experience.
- Configurable thresholds align with the constitution's guidance against hardcoded heuristics.
- Optional duplicate support preserves progressive enhancement.
- Unicode-aware behavior is essential for Arabic support.

### Alternatives Considered

- **Different normalization strategy than Title Check**: Rejected — consistency across features is more valuable.
- **Single monolithic check class**: Rejected — rule composition is the established reference pattern.
- **Hardcoded description thresholds**: Rejected — configurability is a constitutional requirement.
- **Requiring duplicate infrastructure**: Rejected — progressive enhancement is preferred.

## Phase 1: Design

### Proposed Package Structure

- `src/Checks/MetaDescription/Contracts`: Feature-scoped contracts mirroring Title Check.
- `src/Checks/MetaDescription/DTO`: Immutable feature DTOs for input, metadata, normalization results.
- `src/Checks/MetaDescription/Normalization`: Deterministic normalization behavior.
- `src/Checks/MetaDescription/Rules`: Independent rule evaluators for presence, emptiness, separators, length, keyword, duplicate support.
- `src/Checks/MetaDescription/Scoring`: Score contribution assembly with explicit rationale.
- `src/Checks/MetaDescription/Support`: Shared helpers for character classification and configurable length policy.
- `src/Laravel/Support`: Platform-level check registration bridge.

### Core Components and Responsibilities

- `MetaDescriptionCheck`: Implements `MegSEO\Contracts\Check`, orchestrates normalization, rule execution, score contribution assembly, and metadata packaging. Stable identifier: `seo.meta_description`.
- `DeterministicMetaDescriptionNormalizer`: Produces normalized description text (NFKC, trim, collapse whitespace, strip zero-width).
- `MetaDescriptionLengthPolicy`: Accepts explicit numeric thresholds via constructor. Framework-agnostic.
- Rule evaluators (6 rules, mirroring Title Check):
  - `DetectMissingMetaDescription`
  - `DetectEmptyMetaDescription`
  - `DetectSeparatorOnlyMetaDescription`
  - `EvaluateMetaDescriptionLength`
  - `EvaluateFocusKeywordPresence`
  - `EvaluateDuplicateMetaDescriptionSupport`
- `MetaDescriptionScoreContributionBuilder`: Converts rule outcomes into `ScoreSummary` with rationale.
- `MetaDescriptionCharacterClassifier`: Unicode-safe text classification.
- `MetaDescriptionCheckRegistration`: Thin Laravel adapter.

### Public Contracts and Interfaces

- `MetaDescriptionCheck` implements `Check`, uses stable identifier `seo.meta_description`, returns `CheckOutcome`.
- `MetaDescriptionNormalizer`: defines deterministic normalization.
- `SupportsFocusKeyword`: defines optional focus keyword access (shared with Title Check).
- `SupportsDuplicateDescriptions`: defines optional duplicate-description data access.
- Feature DTOs mirror Title Check: `MetaDescriptionCheckInput`, `MetaDescriptionNormalizationResult`, `MetaDescriptionDuplicateMatch`, `MetaDescriptionCheckMetadata`.
- Core `CheckOutcome` DTO reused — no feature-specific result wrapper.

### Pipeline Execution Approach

- Core engine invokes `MetaDescriptionCheck` like any other check.
- Internal deterministic sequence: normalize → severe rules → moderate rules → suggestion rules → score assembly → `CheckOutcome`.
- Rule order is fixed and documented for deterministic output ordering.

### Result Aggregation Strategy

- Issues: missing, empty, whitespace-only, separator-only descriptions.
- Warnings: short descriptions, long descriptions.
- Suggestions: missing focus keyword, duplicate description detected.
- Score contributions derive from findings with explicit rationale; never primary over findings.
- Confidence attached where clarity is added (keyword absence, duplicate detection).
- Metadata includes stable identifiers, normalized length, keyword/duplicate flags.

### Scoring Philosophy

Mirrors Title Check: additive/subtractive logic with explicit deductions.

| Finding | Deduction |
|---------|-----------|
| Missing description | -40 |
| Empty description | -35 |
| Separator-only | -30 |
| Short description | -15 |
| Long description | -10 |
| Duplicate description | -8 |
| Missing keyword | -5 |
| Clean description | 0 (score: 100) |

### Extension Mechanism for Future Checks

Meta Description Check reinforces the pattern established by Title Check:
- Feature-scoped module under `src/Checks/`
- Composable rule objects
- Deterministic normalizer
- Explicit score builder with rationale
- Stable metadata packaging
- Optional capability degradation

### Laravel Integration Approach

- `MetaDescriptionCheckRegistration` acts as a thin adapter — resolves config values and wires through existing registration.
- Configuration for description length thresholds in `config/megseo.php` under `meta_description` key.
- Core classes remain framework-agnostic; config values passed via constructors.

### Testing Strategy

- **Unit tests**: Normalization, character classification, length policy, rule evaluators, score builder.
- **Contract tests**: Stable identifier, finding shape, metadata consistency, deterministic outputs.
- **Integration tests**: Full check through engine, keyword/duplicate scenarios, Arabic/Unicode, deterministic repeats.
- **Laravel tests**: Registration flow, config consumption.

### Backward Compatibility Considerations

- Stable identifier `seo.meta_description` treated as public contract once released.
- New metadata additive only.
- Threshold configurability versioned in documentation.
- Existing core engine and Title Check behavior remain unchanged.

### Risks, Trade-offs, and Rationale

- **Risk**: Redundant character classifier logic with Title Check.
  - **Mitigation**: Feature-scoped classifier avoids coupling; shared extraction can be evaluated later.
- **Trade-off**: Separate files for MetaDescription vs Title Check increase initial code volume.
  - **Rationale**: Feature independence and clear boundaries outweigh DRY concerns for a 2-feature codebase.
- **Trade-off**: Configurable thresholds add settings.
  - **Rationale**: Configurability matches the constitution.

## Phase 2: Implementation Strategy

1. Build the feature shell under `src/Checks/MetaDescription/` mirroring the Title Check structure.
2. Implement normalization and character classification first (blocking dependency).
3. Implement severe usability rules before moderate quality and suggestion rules.
4. Implement score contribution and metadata packaging once rule outputs are stable.
5. Wire into existing MegSEO check registration and Laravel integration layer.
6. Complete unit, contract, and integration coverage.
7. Update dogfooding example.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| None | N/A | N/A |

# Implementation Plan: Title Check

**Branch**: `002-title-check` | **Date**: 2026-06-13 | **Spec**: [spec.md](/abs/path/c:/laragon/www/MegSEO/specs/002-title-check/spec.md)
**Input**: Feature specification from `/specs/002-title-check/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/plan-template.md` for the execution workflow.

## Summary

Implement the first real MegSEO feature as a feature-scoped Title Check that plugs into the validated core analysis engine, evaluates normalized page titles for presence and quality, and returns deterministic issues, warnings, suggestions, score contributions, confidence signals, and stable metadata. The design should act as the reference implementation for future checks while remaining fully within title scope and preserving Laravel-first developer experience.

## Technical Context

**Language/Version**: PHP 8.2+  
**Primary Dependencies**: Existing MegSEO core analysis engine contracts, Composer package structure, Laravel integration layer, PHP Unicode string handling, project configuration support for tunable title thresholds  
**Storage**: N/A for the check itself; optional duplicate-title support data is supplied through the analysis context  
**Testing**: PHPUnit or Pest for unit, contract, and Laravel integration coverage  
**Target Platform**: Composer-distributed Laravel package with framework-agnostic core check behavior  
**Project Type**: Library / Laravel package feature module  
**Performance Goals**: Title analysis should add only minimal overhead beyond normalization, rule evaluation, and result assembly for a single check run  
**Constraints**: No external APIs, no scraping, no AI generation, no behavior outside title scope, deterministic results for identical inputs, stable public contracts, Arabic and Unicode support as first-class behavior, configurable thresholds whenever practical  
**Scale/Scope**: One production-ready feature module that demonstrates the standard design pattern for future MegSEO checks

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Architecture Before Features**: Pass. The feature follows the approved specification and extends the validated core rather than bypassing it.
- **Feature-Driven Development**: Pass. The implementation is scoped to a dedicated Title feature module rather than generic cross-feature folders.
- **Generic Core, Domain-Specific Features**: Pass. All SEO title logic stays inside the Title feature while the core engine remains generic.
- **Open for Extension**: Pass. The plan builds on core contracts, composition, and configurable thresholds rather than hardcoded, tightly coupled behavior.
- **Quality, Documentation, and Stability**: Pass. The plan includes unit tests, edge-case coverage, Laravel integration validation, documentation, and stable output contract stewardship.
- **Actionable, Educational Output Philosophy**: Pass. Findings are designed around issues, warnings, suggestions, rationale, and confidence rather than vanity scoring.
- **Arabic and Future Language Support**: Pass. Arabic and Unicode handling are explicit design concerns, not compatibility afterthoughts.
- **Laravel-First Developer Experience**: Pass. The feature integrates with the existing package surface consistently through the validated platform conventions.
- **Determinism and Human Override**: Pass. Deterministic normalization, stable identifiers, and explicit rationale/confidence align with the constitution’s consistency and guidance-not-truth principles.

## Project Structure

### Documentation (this feature)

```text
specs/002-title-check/
├── plan.md
├── research.md
├── data-model.md
├── quickstart.md
├── contracts/
│   ├── title-check-api.md
│   ├── title-check-finding-contract.md
│   ├── title-normalization-contract.md
│   └── duplicate-title-support-contract.md
└── tasks.md
```

### Source Code (repository root)

```text
src/
├── Checks/
│   └── Title/
│       ├── Contracts/
│       │   ├── SupportsDuplicateTitles.php
│       │   ├── SupportsFocusKeyword.php
│       │   └── TitleNormalizer.php
│       ├── DTO/
│       │   ├── TitleCheckInput.php
│       │   ├── TitleCheckMetadata.php
│       │   ├── TitleDuplicateMatch.php
│       │   └── TitleNormalizationResult.php
│       ├── Normalization/
│       │   └── DeterministicTitleNormalizer.php
│       ├── Rules/
│       │   ├── DetectMissingTitle.php
│       │   ├── DetectEmptyTitle.php
│       │   ├── DetectSeparatorOnlyTitle.php
│       │   ├── EvaluateTitleLength.php
│       │   ├── EvaluateFocusKeywordPresence.php
│       │   └── EvaluateDuplicateTitleSupport.php
│       ├── Scoring/
│       │   └── TitleScoreContributionBuilder.php
│       ├── Support/
│       │   ├── TitleCharacterClassifier.php
│       │   └── TitleLengthPolicy.php
│       └── TitleCheck.php
├── Laravel/
│   └── Support/
│       └── TitleCheckRegistration.php
└── Contracts/
    └── Check.php

config/
└── megseo.php

tests/
├── Unit/
│   └── Checks/
│       └── Title/
├── Contract/
│   └── Checks/
│       └── Title/
└── Integration/
    ├── Checks/
    │   └── Title/
    └── Laravel/
```

**Structure Decision**: Use a check-scoped module under `src/Checks/Title/` so the Title Check naturally lives alongside future checks (MetaDescription, Canonical, OpenGraph, etc.). Supporting contracts and DTOs remain inside the feature boundary where possible, while Laravel wiring stays minimal and consistent with the existing platform.

## Phase 0: Research Summary

### Key Decisions

- Use a deterministic normalization step before every rule evaluation so title presence, length, keyword checks, and duplicate-title support all reason from the same normalized representation.
- Model the Title Check as a composition of small rule evaluators rather than a monolithic check method. This keeps the feature understandable and turns it into a stronger reference design for future checks.
- Keep recommended title length thresholds configurable through project configuration or feature settings rather than hardcoding them in rule logic.
- Treat duplicate-title support as optional contextual capability rather than mandatory infrastructure. The Title Check should degrade safely when duplicate support data is unavailable.
- Use Unicode-aware and Arabic-safe string handling rules for normalization and content validation so multilingual behavior is built in from the start.
- Emit stable identifiers, metadata, rationale, and confidence in the same structured style expected from the core engine contracts and future dashboards.

### Rationale

- Deterministic normalization is required for stable repeated results and for consistent reasoning across length, keyword, and duplicate-title checks.
- Small rule objects improve maintainability, testability, and third-party comprehensibility while reinforcing composition over inheritance.
- Configurable thresholds align with the constitution’s guidance against hardcoded SEO heuristics when practical.
- Optional duplicate-title support preserves progressive enhancement and avoids forcing external dependencies or data stores into the feature.
- Unicode-aware behavior is essential because Arabic support is a strategic differentiator, not a secondary compatibility concern.
- Stable metadata and rationale make the Title Check useful both to end users and to platform integrators building future UI or reporting experiences.

### Alternatives Considered

- **Single monolithic TitleCheck class for all logic**: Rejected because it would be harder to extend, test, and use as a reference pattern for future checks.
- **Hardcoded title thresholds in rule logic**: Rejected because it conflicts with configurability goals and long-term adaptability.
- **ASCII-oriented normalization only**: Rejected because it would undermine Arabic and Unicode correctness.
- **Requiring duplicate-title infrastructure in all runs**: Rejected because progressive enhancement is preferable and core usefulness must not depend on optional data.

## Phase 1: Design

### Proposed Package Structure

- `src/Checks/Title/Contracts`: Feature-scoped extension and support contracts.
- `src/Checks/Title/DTO`: Immutable feature DTOs for input, metadata, normalization results, and optional duplicate data.
- `src/Checks/Title/Normalization`: Deterministic normalization behavior.
- `src/Checks/Title/Rules`: Independent rule evaluators for presence, emptiness, separators, length, focus keyword presence, and duplicate support.
- `src/Checks/Title/Scoring`: Score contribution assembly with explicit rationale.
- `src/Checks/Title/Support`: Shared helpers for character classification and configurable length policy.
- `src/Laravel/Support`: Platform-level check registration bridge for the Title feature.

### Core Components and Responsibilities

- `TitleCheck`: The feature's core check implementation that conforms to the MegSEO check contract, orchestrates normalization, rule execution, score contribution assembly, and metadata packaging, and emits stable identifiers and outcomes.
- `DeterministicTitleNormalizer`: Produces the normalized title representation used by all downstream rules.
- `TitleLengthPolicy`: Accepts explicit numeric thresholds via constructor and encapsulates recommended range and short/long boundary logic. Framework-agnostic; does not directly read configuration files.
- Rule evaluators:
  - `DetectMissingTitle`
  - `DetectEmptyTitle`
  - `DetectSeparatorOnlyTitle`
  - `EvaluateTitleLength`
  - `EvaluateFocusKeywordPresence`
  - `EvaluateDuplicateTitleSupport`
- `TitleScoreContributionBuilder`: Converts rule outcomes into score contribution data with clear rationale.
- `TitleCharacterClassifier`: Helps distinguish meaningful text from punctuation, separators, and whitespace in multilingual-safe ways.
- `TitleCheckRegistration`: Thin adapter that resolves configuration values and registers the Title Check through the existing MegSEO registration mechanism. Contains no business logic.

### Public Contracts and Interfaces

- `TitleCheck` implements the existing MegSEO `Check` contract, uses stable public identifier `seo.title`, and returns `CheckOutcome`.
- `TitleNormalizer`: defines deterministic normalization behavior for feature logic.
- `SupportsFocusKeyword`: defines access to optional focus keyword input in a feature-safe manner.
- `SupportsDuplicateTitles`: defines access to optional duplicate-title support data.
- Feature DTOs:
  - `TitleCheckInput`: encapsulates raw title, optional focus keyword, and optional duplicate support data
  - `TitleNormalizationResult`: stores raw and normalized title values plus normalization metadata
  - `TitleCheckMetadata`: packages stable metadata for dashboards and reporting
- The core `CheckOutcome` DTO is reused for all feature output — no feature-specific result wrapper is introduced.

### Pipeline Execution Approach

- The existing core engine invokes `TitleCheck` like any other check.
- Inside `TitleCheck`, a deterministic internal sequence runs:
  1. Build normalized input
  2. Evaluate severe title-absence/usability rules
  3. Evaluate moderate title-quality rules
  4. Evaluate suggestion-oriented rules such as focus keyword presence
  5. Evaluate optional duplicate-title support when data is available
  6. Assemble score contribution, confidence signals, stable identifiers, and metadata into a `CheckOutcome`
- Internal rule order should remain fixed and documented to preserve deterministic output ordering.

### Result Aggregation Strategy

- The feature emits issues for severe failures such as missing, empty, whitespace-only, and separator-only titles.
- The feature emits warnings for moderate concerns such as overly short or overly long titles.
- The feature emits suggestions for improvement-oriented guidance such as absent focus keyword support.
- Score contribution logic remains supportive and secondary to findings, with explicit rationale for every meaningful score effect.
- Confidence values are attached only where they add clarity, such as duplicate-title support or keyword-presence interpretation.
- Metadata must include stable identifiers and contextual fields suitable for future dashboard or reporting features.

### Execution Policy Strategy

- Title Check behavior should not throw avoidable feature-level errors for missing or unusable title data; these cases are expected findings, not exceptional failures.
- Optional duplicate-title support must degrade gracefully when support data is absent.
- Any internal normalization or rule-processing anomalies should produce deterministic, policy-compatible behavior consistent with the existing engine’s execution policy expectations.

### Extension Mechanism for Future Checks

- The Title feature should demonstrate a reusable pattern of:
  - a feature-scoped input DTO
  - a deterministic normalizer
  - small composable rule objects
  - explicit score contribution builder
  - stable metadata packaging
- Future checks should be able to follow the same internal composition style without requiring changes to the core engine.
- Configurable thresholds and feature settings should illustrate how future features can remain adaptable without baking all heuristics into code.

### Laravel Integration Approach

- Register the Title Check through the existing MegSEO Laravel integration mechanism so it behaves like any other feature-level check.
- Surface configuration for recommended title length policy and related feature toggles through `config/megseo.php` or equivalent existing package configuration.
- Laravel configuration values are resolved at the integration boundary and passed into framework-agnostic Core classes (e.g. `TitleLengthPolicy`) via their constructors — Core classes never read Laravel config directly.
- Keep Laravel-specific code limited to registration and configuration wiring, not feature rule logic.
- Preserve the established developer experience so the feature feels native to the existing MegSEO package.

### Testing Strategy

- **Unit tests**
  - deterministic title normalization
  - Unicode and Arabic-safe text handling
  - missing, empty, whitespace-only, and separator-only title classification
  - short and long title threshold behavior
  - focus keyword presence evaluation
  - duplicate-title support degradation when data is absent
- **Contract tests**
  - stable check identifier behavior
  - public finding shape and metadata consistency
  - score contribution rationale presence
  - deterministic repeated outputs for identical inputs
- **Integration tests**
  - full Title Check execution through the MegSEO engine
  - Laravel registration and consumption flow
  - mixed-language and Arabic title scenarios
  - repeated identical runs produce identical outputs

### Backward Compatibility Considerations

- Treat the Title Check’s stable identifier, finding metadata shape, and emitted result structure as public observable contracts once released.
- New finding metadata must be additive where possible.
- Threshold configurability should not silently change default consumer expectations without versioned documentation.
- The feature’s role as a reference implementation means internal naming and structure choices should remain readable and stable enough for third-party developers to learn from.

### Risks, Trade-offs, and Rationale

- **Risk**: Overfitting the Title Check to one language or character model.
  - **Mitigation**: Use Unicode-aware classification and explicit Arabic-safe handling from the start.
- **Risk**: Too much score emphasis could dilute MegSEO’s guidance-first philosophy.
  - **Mitigation**: Keep findings and rationale primary; score contributions remain secondary.
- **Risk**: Duplicate-title support could accidentally become a hidden dependency.
  - **Mitigation**: Design it as optional contextual input and test safe degradation explicitly.
- **Trade-off**: Smaller rule classes increase the number of feature files.
  - **Rationale**: The clarity, testability, and reference-value gains outweigh the extra structure.
- **Trade-off**: Configurable thresholds introduce more settings to document.
  - **Rationale**: Configurability better matches MegSEO’s constitution than rigid hardcoded heuristics.

## Phase 2: Implementation Strategy

1. Build the feature shell under `src/Checks/Title/` with feature-scoped DTOs, contracts, normalization, and rule directories.
2. Implement deterministic title normalization and character classification first, because every downstream rule depends on them.
3. Implement severe usability rules before moderate quality and suggestion rules.
4. Implement score contribution and metadata packaging once the core rule outputs are stable.
5. Wire the feature into the existing MegSEO check registration flow and Laravel integration layer.
6. Complete unit, contract, and integration coverage across English, Arabic, Unicode, and edge-case title scenarios.
7. Document the feature clearly enough that it functions as the model for future MegSEO checks.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| None | N/A | N/A |

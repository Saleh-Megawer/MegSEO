# Tasks: Canonical Check

**Input**: Design documents from `/specs/004-canonical-check/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Tests are required for this feature by the MegSEO constitution and specification.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- Source code in `src/Checks/Canonical/`
- Tests in `tests/`
- Feature docs in `specs/004-canonical-check/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Establish feature module directory structure under `src/Checks/Canonical/` and create test directories

- [x] T001 Create feature module directory structure in `src/Checks/Canonical/Contracts/`, `src/Checks/Canonical/DTO/`, `src/Checks/Canonical/Normalization/`, `src/Checks/Canonical/Rules/`, `src/Checks/Canonical/Scoring/`, and `src/Checks/Canonical/Support/`
- [x] T002 [P] Create test directory structure in `tests/Unit/Checks/Canonical/`, `tests/Contract/Checks/Canonical/`, and `tests/Integration/Checks/Canonical/`
- [x] T003 [P] Extend `config/megseo.php` with canonical check configuration (`canonical.strict_mode`, `canonical.warn_relative`, `canonical.warn_cross_domain`), default values, and env variable support

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Feature contracts, immutable DTOs, deterministic URL normalization, and URL validation helpers required by all rule evaluators

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

### Contracts

- [x] T004 [P] Create `CanonicalUrlNormalizer` contract in `src/Checks/Canonical/Contracts/CanonicalUrlNormalizer.php` — defines deterministic URL normalization: `normalize(?string $canonicalUrl, ?string $pageUrl): CanonicalUrlNormalizationResult`
- [x] T005 [P] Create `SupportsPageUrl` contract in `src/Checks/Canonical/Contracts/SupportsPageUrl.php` — defines access to optional page URL with `pageUrlSupplied(): bool` and `getNormalizedPageUrl(): ?string`

### DTOs

- [x] T006 [P] Create `CanonicalCheckInput` DTO in `src/Checks/Canonical/DTO/CanonicalCheckInput.php` — immutable, wraps canonical URL, array of canonical URLs (for multiple detection), optional page URL, and attributes
- [x] T007 [P] Create `CanonicalUrlNormalizationResult` DTO in `src/Checks/Canonical/DTO/CanonicalUrlNormalizationResult.php` — immutable, stores raw canonical, normalized canonical, raw page URL, normalized page URL, normalization flags
- [x] T008 [P] Create `CanonicalUrlMatchReport` DTO in `src/Checks/Canonical/DTO/CanonicalUrlMatchReport.php` — immutable, stores self-referencing flag, cross-domain flag, relative flag, match details
- [x] T009 [P] Create `CanonicalCheckMetadata` DTO in `src/Checks/Canonical/DTO/CanonicalCheckMetadata.php` — immutable, stores check identifier, rule identifiers, URL match data, normalization flags

### Normalization

- [x] T010 Implement `DeterministicCanonicalUrlNormalizer` in `src/Checks/Canonical/Normalization/DeterministicCanonicalUrlNormalizer.php` — normalizes URLs: lowercase scheme/host, strip default ports, remove trailing slashes, sort query params, decode percent-encoding; implements `CanonicalUrlNormalizer`

### Support Helpers

- [x] T011 [P] Implement `CanonicalUrlValidator` in `src/Checks/Canonical/Support/CanonicalUrlValidator.php` — validates URL structure: scheme must be http/https, host must be present, path must be well-formed; handles IDN and Unicode URLs

### Phase 2 Tests

- [x] T012 [P] Write unit tests for `CanonicalCheckInput`, `CanonicalUrlNormalizationResult`, `CanonicalUrlMatchReport`, and `CanonicalCheckMetadata` DTO immutability in `tests/Unit/Checks/Canonical/DtoImmutabilityTest.php`
- [x] T013 [P] Write unit tests for `DeterministicCanonicalUrlNormalizer` — identical inputs produce identical outputs, trailing slash handling, protocol normalization, port stripping, query param sorting, IDN handling in `tests/Unit/Checks/Canonical/NormalizationTest.php`
- [x] T014 [P] Write unit tests for `CanonicalUrlValidator` — valid URLs, invalid schemes, missing hosts, relative URLs, IDN URLs, malformed URLs in `tests/Unit/Checks/Canonical/UrlValidatorTest.php`
- [x] T015 [P] Write contract tests for `CanonicalUrlNormalizer` and `SupportsPageUrl` interfaces in `tests/Contract/Checks/Canonical/CanonicalContractsTest.php`

**Checkpoint**: Foundation ready — user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Analyze a Single Canonical Tag Safely (Priority: P1) 🎯 MVP

**Goal**: Evaluate canonical presence, emptiness, URL validity, and multiple canonical detection; return correct issues

**Independent Test**: Submit content contexts with valid, missing, empty, invalid, and multiple canonical tags and verify correct issue categories

### Tests for User Story 1

- [x] T016 [P] [US1] Write unit tests for `DetectMissingCanonical` rule in `tests/Unit/Checks/Canonical/Rules/DetectMissingCanonicalTest.php`
- [x] T017 [P] [US1] Write unit tests for `DetectEmptyCanonical` rule in `tests/Unit/Checks/Canonical/Rules/DetectEmptyCanonicalTest.php`
- [x] T018 [P] [US1] Write unit tests for `DetectInvalidCanonicalUrl` rule in `tests/Unit/Checks/Canonical/Rules/DetectInvalidCanonicalUrlTest.php`
- [x] T019 [P] [US1] Write unit tests for `DetectMultipleCanonicals` rule in `tests/Unit/Checks/Canonical/Rules/DetectMultipleCanonicalsTest.php`
- [x] T020 [P] [US1] Write contract tests for Canonical Check API behavior (missing, empty, invalid, multiple, valid) in `tests/Contract/Checks/Canonical/CanonicalCheckContractTest.php`
- [x] T021 [P] [US1] Write integration tests for full US1 scenarios through the MegSEO engine in `tests/Integration/Checks/Canonical/CanonicalCheckIntegrationTest.php`

### Implementation for User Story 1

- [x] T022 [P] [US1] Implement `DetectMissingCanonical` rule in `src/Checks/Canonical/Rules/DetectMissingCanonical.php` — produces an `AnalysisIssue` when canonical data is missing/null
- [x] T023 [P] [US1] Implement `DetectEmptyCanonical` rule in `src/Checks/Canonical/Rules/DetectEmptyCanonical.php` — produces an `AnalysisIssue` when canonical URL is empty string
- [x] T024 [P] [US1] Implement `DetectInvalidCanonicalUrl` rule in `src/Checks/Canonical/Rules/DetectInvalidCanonicalUrl.php` — produces an `AnalysisIssue` when canonical URL fails validation (uses `CanonicalUrlValidator`)
- [x] T025 [P] [US1] Implement `DetectMultipleCanonicals` rule in `src/Checks/Canonical/Rules/DetectMultipleCanonicals.php` — produces an `AnalysisIssue` when multiple canonical values are detected
- [x] T026 [US1] Implement `CanonicalCheck` in `src/Checks/Canonical/CanonicalCheck.php` — implements `MegSEO\Contracts\Check`; orchestrates normalization, runs US1 rules in fixed order, collects findings in a `CheckOutcome`; uses stable identifier `'seo.canonical'`

**Checkpoint**: User Story 1 functional — valid canonicals pass, missing/empty/invalid/multiple produce issues

---

## Phase 4: User Story 2 - Provide Canonical Quality Guidance (Priority: P2)

**Goal**: Add self-referencing evaluation, relative URL detection, and cross-domain canonical detection

**Independent Test**: Run Canonical Check with self-referencing, relative, and cross-domain canonical patterns; verify appropriate warnings and suggestions

### Tests for User Story 2

- [x] T027 [P] [US2] Write unit tests for `EvaluateSelfReferencingCanonical` rule in `tests/Unit/Checks/Canonical/Rules/EvaluateSelfReferencingCanonicalTest.php`
- [x] T028 [P] [US2] Write unit tests for `EvaluateRelativeCanonicalUrl` rule in `tests/Unit/Checks/Canonical/Rules/EvaluateRelativeCanonicalUrlTest.php`
- [x] T029 [P] [US2] Write unit tests for `EvaluateCrossDomainCanonical` rule in `tests/Unit/Checks/Canonical/Rules/EvaluateCrossDomainCanonicalTest.php`
- [x] T030 [P] [US2] Write integration tests for self-referencing, relative, and cross-domain scenarios through the MegSEO engine in `tests/Integration/Checks/Canonical/CanonicalCheckQualityTest.php`

### Implementation for User Story 2

- [x] T031 [P] [US2] Implement `EvaluateSelfReferencingCanonical` rule in `src/Checks/Canonical/Rules/EvaluateSelfReferencingCanonical.php` — compares normalized canonical against normalized page URL; returns suggestion when canonical does not self-reference
- [x] T032 [P] [US2] Implement `EvaluateRelativeCanonicalUrl` rule in `src/Checks/Canonical/Rules/EvaluateRelativeCanonicalUrl.php` — produces an `AnalysisWarning` when canonical URL is relative (starts with `/` or lacks scheme)
- [x] T033 [P] [US2] Implement `EvaluateCrossDomainCanonical` rule in `src/Checks/Canonical/Rules/EvaluateCrossDomainCanonical.php` — produces an `AnalysisSuggestion` when canonical hostname differs from page URL hostname
- [x] T034 [US2] Update `CanonicalCheck` in `src/Checks/Canonical/CanonicalCheck.php` — integrate US2 rules (self-referencing, relative, cross-domain) into the fixed execution order after US1 rules; gracefully degrade when page URL is absent

**Checkpoint**: User Stories 1 and 2 independently functional

---

## Phase 5: User Story 3 - Act as the Reference Technical SEO Check Pattern (Priority: P3)

**Goal**: Ensure determinism, stable identifiers, score contributions with rationale, metadata packaging, IDN/Unicode handling, and Laravel integration

**Independent Test**: Repeat identical runs and verify identical outputs; test IDN/Unicode URLs; verify Laravel registration flow

### Tests for User Story 3

- [x] T035 [P] [US3] Write unit tests for `CanonicalScoreContributionBuilder` in `tests/Unit/Checks/Canonical/ScoreContributionBuilderTest.php`
- [x] T036 [P] [US3] Write contract tests for deterministic repeated runs, stable identifiers, and metadata consistency in `tests/Contract/Checks/Canonical/DeterminismContractTest.php`
- [x] T037 [P] [US3] Write integration tests for IDN/Unicode URL scenarios in `tests/Integration/Checks/Canonical/IdnUnicodeTest.php`
- [x] T038 [P] [US3] Write Laravel integration tests for Canonical Check registration, configuration, and consumption flow in `tests/Integration/Laravel/CanonicalCheckRegistrationTest.php`

### Implementation for User Story 3

- [x] T039 [US3] Implement `CanonicalScoreContributionBuilder` in `src/Checks/Canonical/Scoring/CanonicalScoreContributionBuilder.php` — assembles score contributions with explicit rationale; maps rule findings to score impact values per plan scoring table
- [x] T040 [US3] Update `CanonicalCheck` in `src/Checks/Canonical/CanonicalCheck.php` — integrate score contribution assembly, metadata packaging (`CanonicalCheckMetadata`), and ensure deterministic ordering
- [x] T041 [P] [US3] Implement `CanonicalCheckRegistration` in `src/Laravel/Support/CanonicalCheckRegistration.php` — thin adapter resolving config values and wiring through existing registration; contains no business logic
- [x] T042 [P] [US3] Add Canonical Check class to `config/megseo.php` default `'checks'` array

**Checkpoint**: All user stories independently functional — Canonical Check complete

---

## Phase 6: Polish & Cross-Cutting Concerns

- [x] T043 [P] Update quickstart documentation in `specs/004-canonical-check/quickstart.md` with actual code examples
- [x] T044 [P] Validate public contract documentation in `specs/004-canonical-check/contracts/` against implemented behavior
- [x] T045 Add edge-case regression tests — IDN domains, mixed-case URLs, trailing slash variants, empty page URL with canonical, query string ordering in `tests/Integration/Checks/Canonical/CanonicalCheckEdgeCasesTest.php`
- [x] T046 [P] Verify backwards compatibility — existing core engine, Title Check, and Meta Description Check behavior unchanged
- [x] T047 Run full feature-level API review and code cleanup across `src/Checks/Canonical/`
- [x] T048 Validate that the feature follows the Title Check / Meta Description Check reference implementation pattern

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies
- **Foundational (Phase 2)**: Depends on Setup — blocks all user stories
- **US1 (Phase 3)**: Depends on Phase 2
- **US2 (Phase 4)**: Depends on Phase 2 + US1's `CanonicalCheck` structure (T026)
- **US3 (Phase 5)**: Depends on Phase 2 + US1 + US2
- **Polish (Phase 6)**: Depends on all user stories

### Parallel Opportunities

- Phase 2: Contracts T004–T005, DTOs T006–T009, and support T011 can all run in parallel
- Phase 3: All 4 rule tests (T016–T019) + contract + integration tests can run in parallel; rules (T022–T025) can run in parallel
- Phase 4: All 3 rule tests (T027–T029) + integration test can run in parallel; rules (T031–T033) can run in parallel
- Phase 5: All tests (T035–T038) can run in parallel; Laravel tasks (T041–T042) can run in parallel
- Phase 6: All polish tasks can run in parallel

---

## Notes

- `[P]` tasks touch different files and can be parallelized safely
- `CanonicalCheck` implements `MegSEO\Contracts\Check` with stable identifier `'seo.canonical'`
- All DTOs follow the existing `final readonly class` pattern
- URL normalization is the canonical equivalent of text normalization — happens once before rule evaluation
- Page URL is optional; absence disables self-referencing and cross-domain checks gracefully
- Cross-domain canonicals produce suggestions (may be intentional for syndication)
- Relative canonicals produce warnings (recommend absolute URLs)
- Core feature classes must be framework-agnostic
- Deterministic outputs for identical inputs are non-negotiable

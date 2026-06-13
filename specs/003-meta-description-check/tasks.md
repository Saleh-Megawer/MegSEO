# Tasks: Meta Description Check

**Input**: Design documents from `/specs/003-meta-description-check/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Tests are required for this feature by the MegSEO constitution and specification.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- Single project package structure at repository root
- Source code in `src/Checks/MetaDescription/`
- Tests in `tests/`
- Feature docs in `specs/003-meta-description-check/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Establish feature module directory structure under `src/Checks/MetaDescription/` and create test directories

- [x] T001 Create feature module directory structure in `src/Checks/MetaDescription/Contracts/`, `src/Checks/MetaDescription/DTO/`, `src/Checks/MetaDescription/Normalization/`, `src/Checks/MetaDescription/Rules/`, `src/Checks/MetaDescription/Scoring/`, and `src/Checks/MetaDescription/Support/`
- [x] T002 [P] Create test directory structure in `tests/Unit/Checks/MetaDescription/`, `tests/Contract/Checks/MetaDescription/`, and `tests/Integration/Checks/MetaDescription/`
- [x] T003 [P] Extend `config/megseo.php` with meta description length thresholds (`meta_description.min_length`, `meta_description.max_length`, `meta_description.short_threshold`, `meta_description.long_threshold`), default values, and env variable support

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Feature contracts, immutable DTOs, deterministic normalization, and shared support helpers required by all rule evaluators

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

### Contracts

- [x] T004 [P] Create `SupportsDuplicateDescriptions` contract in `src/Checks/MetaDescription/Contracts/SupportsDuplicateDescriptions.php` — defines access to optional duplicate-description support data with `duplicateDataAvailable(): bool` and `getDuplicateMatches(): array`
- [x] T005 [P] Create `SupportsFocusKeyword` contract in `src/Checks/MetaDescription/Contracts/SupportsFocusKeyword.php` — defines access to optional focus keyword input with `keywordSupplied(): bool` and `getNormalizedKeyword(): ?string`
- [x] T006 [P] Create `MetaDescriptionNormalizer` contract in `src/Checks/MetaDescription/Contracts/MetaDescriptionNormalizer.php` — defines deterministic normalization: `normalize(?string $rawDescription, ?string $focusKeyword): MetaDescriptionNormalizationResult`

### DTOs

- [x] T007 [P] Create `MetaDescriptionCheckInput` DTO in `src/Checks/MetaDescription/DTO/MetaDescriptionCheckInput.php` — immutable, wraps raw description, optional focus keyword, optional duplicate support data, and feature attributes
- [x] T008 [P] Create `MetaDescriptionNormalizationResult` DTO in `src/Checks/MetaDescription/DTO/MetaDescriptionNormalizationResult.php` — immutable, stores raw description, normalized description, normalized focus keyword, normalization flags (metadata)
- [x] T009 [P] Create `MetaDescriptionDuplicateMatch` DTO in `src/Checks/MetaDescription/DTO/MetaDescriptionDuplicateMatch.php` — immutable, stores matched description, matched reference, match reason
- [x] T010 [P] Create `MetaDescriptionCheckMetadata` DTO in `src/Checks/MetaDescription/DTO/MetaDescriptionCheckMetadata.php` — immutable, stores check identifier, rule identifiers, normalized length, duplicate support used flag, focus keyword supplied flag

### Normalization

- [x] T011 Implement `DeterministicMetaDescriptionNormalizer` in `src/Checks/MetaDescription/Normalization/DeterministicMetaDescriptionNormalizer.php` — produces canonical normalized description representation: trims whitespace, collapses internal whitespace to single spaces, normalizes Unicode (NFKC), strips zero-width characters, persists normalization flags; implements `MetaDescriptionNormalizer`

### Support Helpers

- [x] T012 [P] Implement `MetaDescriptionCharacterClassifier` in `src/Checks/MetaDescription/Support/MetaDescriptionCharacterClassifier.php` — distinguishes meaningful text from punctuation, separators, and whitespace in Unicode-safe ways; Arabic-aware
- [x] T013 [P] Implement `MetaDescriptionLengthPolicy` in `src/Checks/MetaDescription/Support/MetaDescriptionLengthPolicy.php` — accepts explicit numeric thresholds via constructor; provides `isShort(int $length): bool`, `isLong(int $length): bool`, `getRecommendedMin(): int`, `getRecommendedMax(): int`; framework-agnostic — does not read configuration files directly

### Phase 2 Tests

- [x] T014 [P] Write unit tests for `MetaDescriptionCheckInput`, `MetaDescriptionNormalizationResult`, `MetaDescriptionDuplicateMatch`, and `MetaDescriptionCheckMetadata` DTO immutability in `tests/Unit/Checks/MetaDescription/DtoImmutabilityTest.php`
- [x] T015 [P] Write unit tests for `DeterministicMetaDescriptionNormalizer` — identical inputs produce identical outputs, whitespace handling, Unicode NFKC normalization, Arabic text, zero-width character stripping in `tests/Unit/Checks/MetaDescription/NormalizationTest.php`
- [x] T016 [P] Write unit tests for `MetaDescriptionCharacterClassifier` — punctuation-only, separator-only, Arabic text classification, mixed scripts in `tests/Unit/Checks/MetaDescription/CharacterClassifierTest.php`
- [x] T017 [P] Write unit tests for `MetaDescriptionLengthPolicy` — constructor-provided thresholds, short/long boundary behavior, default values, framework-agnostic behavior in `tests/Unit/Checks/MetaDescription/LengthPolicyTest.php`
- [x] T018 [P] Write contract tests for `MetaDescriptionNormalizer`, `SupportsFocusKeyword`, and `SupportsDuplicateDescriptions` interfaces in `tests/Contract/Checks/MetaDescription/MetaDescriptionContractsTest.php`

**Checkpoint**: Foundation ready — user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Analyze a Single Meta Description Safely (Priority: P1) 🎯 MVP

**Goal**: Evaluate description presence, emptiness, whitespace-only, separator-only, short, and long descriptions; return correct issues and warnings

**Independent Test**: Submit content contexts with valid, missing, empty, whitespace-only, separator-only, short, and long descriptions and verify the correct issue/warning categories are returned

### Tests for User Story 1

- [x] T019 [P] [US1] Write unit tests for `DetectMissingMetaDescription` rule in `tests/Unit/Checks/MetaDescription/Rules/DetectMissingMetaDescriptionTest.php`
- [x] T020 [P] [US1] Write unit tests for `DetectEmptyMetaDescription` rule (covers empty, whitespace-only) in `tests/Unit/Checks/MetaDescription/Rules/DetectEmptyMetaDescriptionTest.php`
- [x] T021 [P] [US1] Write unit tests for `DetectSeparatorOnlyMetaDescription` rule in `tests/Unit/Checks/MetaDescription/Rules/DetectSeparatorOnlyMetaDescriptionTest.php`
- [x] T022 [P] [US1] Write unit tests for `EvaluateMetaDescriptionLength` rule in `tests/Unit/Checks/MetaDescription/Rules/EvaluateMetaDescriptionLengthTest.php`
- [x] T023 [P] [US1] Write contract tests for Meta Description Check API behavior (missing, empty, short, long, valid descriptions) in `tests/Contract/Checks/MetaDescription/MetaDescriptionCheckContractTest.php`
- [x] T024 [P] [US1] Write integration tests for full US1 scenarios through the MegSEO engine in `tests/Integration/Checks/MetaDescription/MetaDescriptionCheckIntegrationTest.php`

### Implementation for User Story 1

- [x] T025 [P] [US1] Implement `DetectMissingMetaDescription` rule in `src/Checks/MetaDescription/Rules/DetectMissingMetaDescription.php` — produces an `AnalysisIssue` when description data is missing/null
- [x] T026 [P] [US1] Implement `DetectEmptyMetaDescription` rule in `src/Checks/MetaDescription/Rules/DetectEmptyMetaDescription.php` — produces an `AnalysisIssue` when normalized description is empty string
- [x] T027 [P] [US1] Implement `DetectSeparatorOnlyMetaDescription` rule in `src/Checks/MetaDescription/Rules/DetectSeparatorOnlyMetaDescription.php` — produces an `AnalysisIssue` when description contains only punctuation/separators/whitespace (uses `MetaDescriptionCharacterClassifier`)
- [x] T028 [P] [US1] Implement `EvaluateMetaDescriptionLength` rule in `src/Checks/MetaDescription/Rules/EvaluateMetaDescriptionLength.php` — produces an `AnalysisWarning` when description is shorter or longer than configured thresholds (uses `MetaDescriptionLengthPolicy`)
- [x] T029 [US1] Implement `MetaDescriptionCheck` in `src/Checks/MetaDescription/MetaDescriptionCheck.php` — implements `MegSEO\Contracts\Check`; orchestrates normalization, runs US1 rules in fixed order, collects findings in a `CheckOutcome`; degrades safely when description is missing; uses stable identifier `'seo.meta_description'`

**Checkpoint**: User Story 1 should be fully functional — valid descriptions pass, missing/empty/separator-only produce issues, short/long produce warnings

---

## Phase 4: User Story 2 - Provide Search-Relevant Description Guidance (Priority: P2)

**Goal**: Add focus keyword presence analysis and optional duplicate-description support evaluation

**Independent Test**: Run Meta Description Check with and without a focus keyword, and with and without duplicate-description support data; verify keyword-presence suggestions and duplicate findings degrade safely

### Tests for User Story 2

- [x] T030 [P] [US2] Write unit tests for `EvaluateFocusKeywordPresence` rule in `tests/Unit/Checks/MetaDescription/Rules/EvaluateFocusKeywordPresenceTest.php`
- [x] T031 [P] [US2] Write unit tests for `EvaluateDuplicateMetaDescriptionSupport` rule in `tests/Unit/Checks/MetaDescription/Rules/EvaluateDuplicateMetaDescriptionSupportTest.php`
- [x] T032 [P] [US2] Write integration tests for keyword presence and duplicate-description scenarios through the MegSEO engine in `tests/Integration/Checks/MetaDescription/MetaDescriptionCheckKeywordDuplicateTest.php`

### Implementation for User Story 2

- [x] T033 [P] [US2] Implement `EvaluateFocusKeywordPresence` rule in `src/Checks/MetaDescription/Rules/EvaluateFocusKeywordPresence.php` — when keyword is supplied and absent from normalized description, produces an `AnalysisSuggestion` with confidence; when keyword is present, produces no finding; degrades safely when keyword is not supplied
- [x] T034 [P] [US2] Implement `EvaluateDuplicateMetaDescriptionSupport` rule in `src/Checks/MetaDescription/Rules/EvaluateDuplicateMetaDescriptionSupport.php` — when duplicate support data is available and the normalized description matches another page, produces a finding or metadata signal; degrades safely when support data is absent (no errors, no false findings)
- [x] T035 [US2] Update `MetaDescriptionCheck` in `src/Checks/MetaDescription/MetaDescriptionCheck.php` — integrate US2 rules (focus keyword, duplicate-description) into the fixed execution order after US1 rules

**Checkpoint**: User Stories 1 and 2 should both work independently — keyword guidance and duplicate support provide additional actionable findings

---

## Phase 5: User Story 3 - Act as a Consistent Feature Pattern (Priority: P3)

**Goal**: Ensure determinism, stable identifiers, score contributions with rationale, confidence signaling, metadata packaging, Arabic/Unicode handling, and Laravel integration — reinforcing the Title Check reference pattern

**Independent Test**: Repeat identical analysis runs and verify identical outputs; test Arabic/Unicode descriptions produce correct findings; verify stable metadata and identifiers; verify Laravel registration flow

### Tests for User Story 3

- [x] T036 [P] [US3] Write unit tests for `MetaDescriptionScoreContributionBuilder` in `tests/Unit/Checks/MetaDescription/ScoreContributionBuilderTest.php`
- [x] T037 [P] [US3] Write contract tests for deterministic repeated runs, stable identifiers, and metadata consistency in `tests/Contract/Checks/MetaDescription/DeterminismContractTest.php`
- [x] T038 [P] [US3] Write integration tests for Arabic and Unicode description scenarios in `tests/Integration/Checks/MetaDescription/ArabicUnicodeTest.php`
- [x] T039 [P] [US3] Write Laravel integration tests for Meta Description Check registration, configuration, and consumption flow in `tests/Integration/Laravel/MetaDescriptionCheckRegistrationTest.php`

### Implementation for User Story 3

- [x] T040 [US3] Implement `MetaDescriptionScoreContributionBuilder` in `src/Checks/MetaDescription/Scoring/MetaDescriptionScoreContributionBuilder.php` — assembles score contributions from rule outcomes with explicit rationale strings; maps rule findings to score impact values
- [x] T041 [US3] Update `MetaDescriptionCheck` in `src/Checks/MetaDescription/MetaDescriptionCheck.php` — integrate score contribution assembly, metadata packaging (`MetaDescriptionCheckMetadata`), and ensure deterministic ordering of all outputs in the `CheckOutcome`
- [x] T042 [P] [US3] Implement `MetaDescriptionCheckRegistration` in `src/Laravel/Support/MetaDescriptionCheckRegistration.php` — thin adapter that resolves Laravel configuration values (thresholds, check class) and wires the Meta Description Check into the existing MegSEO registration mechanism; contains no business logic
- [x] T043 [P] [US3] Add Meta Description Check class to `config/megseo.php` default `'checks'` array and wire meta description threshold keys into `MetaDescriptionLengthPolicy` construction at the Laravel integration boundary

**Checkpoint**: All user stories should now be independently functional — the Meta Description Check is complete, deterministic, and follows the Title Check reference pattern

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Documentation, edge-case validation, and final quality assurance

- [x] T044 [P] Update quickstart documentation in `specs/003-meta-description-check/quickstart.md` with actual code examples and validated consumption patterns
- [x] T045 [P] Validate public contract documentation in `specs/003-meta-description-check/contracts/` against implemented behavior
- [x] T046 Add edge-case regression tests — missing description with focus keyword, Arabic descriptions with duplicate support, empty keyword with valid description, zero-length boundaries — in `tests/Integration/Checks/MetaDescription/MetaDescriptionCheckEdgeCasesTest.php`
- [x] T047 [P] Verify backwards compatibility — existing core engine behavior and Title Check are unchanged after Meta Description Check registration; no existing test regressions
- [x] T048 Run full feature-level API review and code cleanup across `src/Checks/MetaDescription/`
- [x] T049 Validate that the feature follows the Title Check reference implementation pattern: verify rule composition style, normalization pipeline, metadata structure, and confidence signaling are consistent

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion — blocks all user stories
- **User Stories (Phases 3–5)**: Depend on Foundational completion
  - US1 (Phase 3) depends only on Phase 2
  - US2 (Phase 4) depends on Phase 2 + US1's `MetaDescriptionCheck` structure (T029)
  - US3 (Phase 5) depends on Phase 2 + US1 + US2 structure
- **Polish (Phase 6)**: Depends on all user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Starts after Foundational. Delivers the MVP description presence/quality analysis.
- **User Story 2 (P2)**: Starts after US1's `MetaDescriptionCheck` exists (T029). Depends on the normalization and rule execution pipeline established by US1.
- **User Story 3 (P3)**: Starts after US1 and US2 rule evaluators exist. Depends on stable finding shapes, identifiers, and metadata from prior stories.

### Within Each Phase

- Contracts and DTOs before rule implementations
- Normalization and support helpers before rules that consume them
- Tests written and failing before implementation is considered complete
- Severe rules (missing, empty, separator-only) before moderate rules (length, keyword, duplicate)
- Score contribution and metadata after rule outputs are stable

### Parallel Opportunities

- Phase 1: T002 and T003 can run in parallel
- Phase 2: All contracts (T004–T006) and DTOs (T007–T010) can run in parallel; T012 and T013 can run in parallel; all Phase 2 tests (T014–T018) can run in parallel after their subjects exist
- Phase 3: All US1 tests (T019–T024) and rules (T025–T028) can run in parallel
- Phase 4: All US2 tests (T030–T032) and rules (T033–T034) can run in parallel
- Phase 5: All US3 tests (T036–T039) and Laravel tasks (T042–T043) can run in parallel
- Phase 6: All polish tasks (T044–T049) can run in parallel

---

## Parallel Example: Phase 2 (Foundational)

```bash
# Launch contracts together:
Task: "Create SupportsDuplicateDescriptions contract in src/Checks/MetaDescription/Contracts/SupportsDuplicateDescriptions.php"
Task: "Create SupportsFocusKeyword contract in src/Checks/MetaDescription/Contracts/SupportsFocusKeyword.php"
Task: "Create MetaDescriptionNormalizer contract in src/Checks/MetaDescription/Contracts/MetaDescriptionNormalizer.php"

# Launch DTOs together:
Task: "Create MetaDescriptionCheckInput DTO in src/Checks/MetaDescription/DTO/MetaDescriptionCheckInput.php"
Task: "Create MetaDescriptionNormalizationResult DTO in src/Checks/MetaDescription/DTO/MetaDescriptionNormalizationResult.php"
Task: "Create MetaDescriptionDuplicateMatch DTO in src/Checks/MetaDescription/DTO/MetaDescriptionDuplicateMatch.php"
Task: "Create MetaDescriptionCheckMetadata DTO in src/Checks/MetaDescription/DTO/MetaDescriptionCheckMetadata.php"

# Launch support helpers together:
Task: "Implement MetaDescriptionCharacterClassifier in src/Checks/MetaDescription/Support/MetaDescriptionCharacterClassifier.php"
Task: "Implement MetaDescriptionLengthPolicy in src/Checks/MetaDescription/Support/MetaDescriptionLengthPolicy.php"
```

## Parallel Example: Phase 3 (User Story 1)

```bash
# Launch US1 rules together:
Task: "Implement DetectMissingMetaDescription rule in src/Checks/MetaDescription/Rules/DetectMissingMetaDescription.php"
Task: "Implement DetectEmptyMetaDescription rule in src/Checks/MetaDescription/Rules/DetectEmptyMetaDescription.php"
Task: "Implement DetectSeparatorOnlyMetaDescription rule in src/Checks/MetaDescription/Rules/DetectSeparatorOnlyMetaDescription.php"
Task: "Implement EvaluateMetaDescriptionLength rule in src/Checks/MetaDescription/Rules/EvaluateMetaDescriptionLength.php"

# Launch US1 tests together:
Task: "Write unit tests for DetectMissingMetaDescription rule"
Task: "Write unit tests for DetectEmptyMetaDescription rule"
Task: "Write unit tests for DetectSeparatorOnlyMetaDescription rule"
Task: "Write unit tests for EvaluateMetaDescriptionLength rule"
Task: "Write contract tests for Meta Description Check API behavior"
Task: "Write integration tests for full US1 scenarios"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (contracts, DTOs, normalization, support)
3. Complete Phase 3: User Story 1 (description presence, emptiness, separator-only, length)
4. Run tests and validate the full US1 acceptance scenarios
5. Deploy as the first functional Meta Description Check MVP

### Incremental Delivery

1. Complete Setup + Foundational to establish the feature shell and normalization pipeline
2. Add User Story 1 to deliver core description presence/quality analysis (issues + warnings)
3. Add User Story 2 to deliver search-relevant guidance (keyword suggestions + duplicate signals)
4. Add User Story 3 to lock down determinism, stable contracts, Arabic/Unicode, metadata, and Laravel integration
5. Finish with polish, edge-case regression coverage, and documentation validation

### Parallel Team Strategy

With multiple developers:

1. One developer establishes feature directories, contracts, and DTO scaffolding while another sets up test infrastructure and config stubs
2. After Foundational completes:
   - Developer A: User Story 1 rules and MetaDescriptionCheck orchestration
   - Developer B: User Story 2 keyword and duplicate rules
   - Developer C: Normalization edge cases and Arabic/Unicode validation
3. After US1 and US2 stabilize:
   - All developers converge on US3 — determinism, scoring, metadata, and Laravel wiring
4. Rejoin for edge-case regression testing, documentation polish, and API review

---

## Notes

- `[P]` tasks touch different files and can be parallelized safely
- `[US1]`, `[US2]`, and `[US3]` map directly to the approved specification user stories
- Every user story includes explicit tests because testing is mandatory for this feature
- `MetaDescriptionCheck` implements `MegSEO\Contracts\Check` with stable identifier `'seo.meta_description'`
- All DTOs follow the existing `final readonly class` pattern from `src/DTO/`
- The feature reuses the core `CheckOutcome` DTO for results — no feature-specific result wrapper
- Normalization happens once before all rule evaluation — rules receive the normalized result
- Duplicate-description support is optional; the feature must degrade safely when data is absent
- The feature follows the Title Check reference implementation — structure, naming, and composability match
- Arabic and Unicode are first-class concerns, not afterthoughts
- Deterministic outputs for identical inputs are non-negotiable
- Core feature classes under `src/Checks/MetaDescription/` must be framework-agnostic — never read Laravel config directly; configuration values are passed via constructors or method parameters at the integration boundary

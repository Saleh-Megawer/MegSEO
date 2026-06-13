# Tasks: Title Check

**Input**: Design documents from `/specs/002-title-check/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Tests are required for this feature by the MegSEO constitution and specification.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- Single project package structure at repository root
- Source code in `src/Checks/Title/`
- Tests in `tests/`
- Feature docs in `specs/002-title-check/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Establish feature module directory structure under `src/Checks/Title/` and create test directories

- [x] T001 Create feature module directory structure in `src/Checks/Title/Contracts/`, `src/Checks/Title/DTO/`, `src/Checks/Title/Normalization/`, `src/Checks/Title/Rules/`, `src/Checks/Title/Scoring/`, and `src/Checks/Title/Support/`
- [x] T002 [P] Create test directory structure in `tests/Unit/Checks/Title/`, `tests/Contract/Checks/Title/`, and `tests/Integration/Checks/Title/`
- [x] T003 [P] Extend `config/megseo.php` with title length thresholds (`title.min_length`, `title.max_length`, `title.short_threshold`, `title.long_threshold`), default values, and env variable support

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Feature contracts, immutable DTOs, deterministic normalization, and shared support helpers required by all rule evaluators

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

### Contracts

- [x] T004 [P] Create `SupportsDuplicateTitles` contract in `src/Checks/Title/Contracts/SupportsDuplicateTitles.php` — defines access to optional duplicate-title support data with `duplicateDataAvailable(): bool` and `getDuplicateMatches(): array`
- [x] T005 [P] Create `SupportsFocusKeyword` contract in `src/Checks/Title/Contracts/SupportsFocusKeyword.php` — defines access to optional focus keyword input with `keywordSupplied(): bool` and `getNormalizedKeyword(): ?string`
- [x] T006 [P] Create `TitleNormalizer` contract in `src/Checks/Title/Contracts/TitleNormalizer.php` — defines deterministic normalization: `normalize(?string $rawTitle, ?string $focusKeyword): TitleNormalizationResult`

### DTOs

- [x] T007 [P] Create `TitleCheckInput` DTO in `src/Checks/Title/DTO/TitleCheckInput.php` — immutable, wraps raw title, optional focus keyword, optional duplicate support data, and feature attributes
- [x] T008 [P] Create `TitleNormalizationResult` DTO in `src/Checks/Title/DTO/TitleNormalizationResult.php` — immutable, stores raw title, normalized title, normalized focus keyword, normalization flags (metadata)
- [x] T009 [P] Create `TitleDuplicateMatch` DTO in `src/Checks/Title/DTO/TitleDuplicateMatch.php` — immutable, stores matched title, matched reference, match reason
- [x] T010 [P] Create `TitleCheckMetadata` DTO in `src/Checks/Title/DTO/TitleCheckMetadata.php` — immutable, stores check identifier, rule identifiers, normalized length, duplicate support used flag, focus keyword supplied flag

### Normalization

- [x] T011 Implement `DeterministicTitleNormalizer` in `src/Checks/Title/Normalization/DeterministicTitleNormalizer.php` — produces canonical normalized title representation: trims whitespace, collapses internal whitespace to single spaces, normalizes Unicode (NFKC), strips zero-width characters, persists normalization flags; implements `TitleNormalizer`

### Support Helpers

- [x] T012 [P] Implement `TitleCharacterClassifier` in `src/Checks/Title/Support/TitleCharacterClassifier.php` — distinguishes meaningful text from punctuation, separators, and whitespace in Unicode-safe ways; Arabic-aware
- [x] T013 [P] Implement `TitleLengthPolicy` in `src/Checks/Title/Support/TitleLengthPolicy.php` — accepts explicit numeric thresholds via constructor; provides `isShort(int $length): bool`, `isLong(int $length): bool`, `getRecommendedMin(): int`, `getRecommendedMax(): int`; framework-agnostic — does not read configuration files directly

### Phase 2 Tests

- [x] T014 [P] Write unit tests for `TitleCheckInput`, `TitleNormalizationResult`, `TitleDuplicateMatch`, and `TitleCheckMetadata` DTO immutability in `tests/Unit/Checks/Title/DtoImmutabilityTest.php`
- [x] T015 [P] Write unit tests for `DeterministicTitleNormalizer` — identical inputs produce identical outputs, whitespace handling, Unicode NFKC normalization, Arabic text, zero-width character stripping in `tests/Unit/Checks/Title/NormalizationTest.php`
- [x] T016 [P] Write unit tests for `TitleCharacterClassifier` — punctuation-only, separator-only, Arabic text classification, mixed scripts in `tests/Unit/Checks/Title/CharacterClassifierTest.php`
- [x] T017 [P] Write unit tests for `TitleLengthPolicy` — constructor-provided thresholds, short/long boundary behavior, default values, framework-agnostic behavior in `tests/Unit/Checks/Title/LengthPolicyTest.php`
- [x] T018 [P] Write contract tests for `TitleNormalizer`, `SupportsFocusKeyword`, and `SupportsDuplicateTitles` interfaces in `tests/Contract/Checks/Title/TitleContractsTest.php`

**Checkpoint**: Foundation ready — user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Analyze a Single Title Safely (Priority: P1) 🎯 MVP

**Goal**: Evaluate title presence, emptiness, whitespace-only, separator-only, short, and long titles; return correct issues and warnings

**Independent Test**: Submit content contexts with valid, missing, empty, whitespace-only, separator-only, short, and long titles and verify the correct issue/warning categories are returned

### Tests for User Story 1

- [x] T019 [P] [US1] Write unit tests for `DetectMissingTitle` rule in `tests/Unit/Checks/Title/Rules/DetectMissingTitleTest.php`
- [x] T020 [P] [US1] Write unit tests for `DetectEmptyTitle` rule (covers empty, whitespace-only, separator-only) in `tests/Unit/Checks/Title/Rules/DetectEmptyTitleTest.php`
- [x] T021 [P] [US1] Write unit tests for `DetectSeparatorOnlyTitle` rule in `tests/Unit/Checks/Title/Rules/DetectSeparatorOnlyTitleTest.php`
- [x] T022 [P] [US1] Write unit tests for `EvaluateTitleLength` rule in `tests/Unit/Checks/Title/Rules/EvaluateTitleLengthTest.php`
- [x] T023 [P] [US1] Write contract tests for Title Check API behavior (missing, empty, short, long, valid titles) in `tests/Contract/Checks/Title/TitleCheckContractTest.php`
- [x] T024 [P] [US1] Write integration tests for full US1 scenarios through the MegSEO engine in `tests/Integration/Checks/Title/TitleCheckIntegrationTest.php`

### Implementation for User Story 1

- [x] T025 [P] [US1] Implement `DetectMissingTitle` rule in `src/Checks/Title/Rules/DetectMissingTitle.php` — produces an `AnalysisIssue` when title data is missing/null
- [x] T026 [P] [US1] Implement `DetectEmptyTitle` rule in `src/Checks/Title/Rules/DetectEmptyTitle.php` — produces an `AnalysisIssue` when normalized title is empty string
- [x] T027 [P] [US1] Implement `DetectSeparatorOnlyTitle` rule in `src/Checks/Title/Rules/DetectSeparatorOnlyTitle.php` — produces an `AnalysisIssue` when title contains only punctuation/separators/whitespace (uses `TitleCharacterClassifier`)
- [x] T028 [P] [US1] Implement `EvaluateTitleLength` rule in `src/Checks/Title/Rules/EvaluateTitleLength.php` — produces an `AnalysisWarning` when title is shorter or longer than configured thresholds (uses `TitleLengthPolicy`)
- [x] T029 [US1] Implement `TitleCheck` in `src/Checks/Title/TitleCheck.php` — implements `MegSEO\Contracts\Check`; orchestrates normalization, runs US1 rules in fixed order, collects findings in a `CheckOutcome`; degrades safely when title is missing; uses stable identifier `'seo.title'`

**Checkpoint**: User Story 1 should be fully functional — valid titles pass, missing/empty/separator-only produce issues, short/long produce warnings

---

## Phase 4: User Story 2 - Provide Search-Relevant Guidance (Priority: P2)

**Goal**: Add focus keyword presence analysis and optional duplicate-title support evaluation

**Independent Test**: Run Title Check with and without a focus keyword, and with and without duplicate-title support data; verify keyword-presence suggestions and duplicate findings degrade safely

### Tests for User Story 2

- [ ] T030 [P] [US2] Write unit tests for `EvaluateFocusKeywordPresence` rule in `tests/Unit/Checks/Title/Rules/EvaluateFocusKeywordPresenceTest.php`
- [ ] T031 [P] [US2] Write unit tests for `EvaluateDuplicateTitleSupport` rule in `tests/Unit/Checks/Title/Rules/EvaluateDuplicateTitleSupportTest.php`
- [ ] T032 [P] [US2] Write integration tests for keyword presence and duplicate-title scenarios through the MegSEO engine in `tests/Integration/Checks/Title/TitleCheckKeywordDuplicateTest.php`

### Implementation for User Story 2

- [ ] T033 [P] [US2] Implement `EvaluateFocusKeywordPresence` rule in `src/Checks/Title/Rules/EvaluateFocusKeywordPresence.php` — when keyword is supplied and absent from normalized title, produces an `AnalysisSuggestion` with confidence; when keyword is present, produces no finding; degrades safely when keyword is not supplied
- [ ] T034 [P] [US2] Implement `EvaluateDuplicateTitleSupport` rule in `src/Checks/Title/Rules/EvaluateDuplicateTitleSupport.php` — when duplicate support data is available and the normalized title matches another page, produces a finding or metadata signal; degrades safely when support data is absent (no errors, no false findings)
- [ ] T035 [US2] Update `TitleCheck` in `src/Checks/Title/TitleCheck.php` — integrate US2 rules (focus keyword, duplicate-title) into the fixed execution order after US1 rules

**Checkpoint**: User Stories 1 and 2 should both work independently — keyword guidance and duplicate support provide additional actionable findings

---

## Phase 5: User Story 3 - Act as the Reference Check Pattern (Priority: P3)

**Goal**: Ensure determinism, stable identifiers, score contributions with rationale, confidence signaling, metadata packaging, Arabic/Unicode handling, and Laravel integration — making the Title Check the reference implementation for future checks

**Independent Test**: Repeat identical analysis runs and verify identical outputs; test Arabic/Unicode titles produce correct findings; verify stable metadata and identifiers; verify Laravel registration flow

### Tests for User Story 3

- [ ] T036 [P] [US3] Write unit tests for `TitleScoreContributionBuilder` in `tests/Unit/Checks/Title/ScoreContributionBuilderTest.php`
- [ ] T037 [P] [US3] Write contract tests for deterministic repeated runs, stable identifiers, and metadata consistency in `tests/Contract/Checks/Title/DeterminismContractTest.php`
- [ ] T038 [P] [US3] Write integration tests for Arabic and Unicode title scenarios in `tests/Integration/Checks/Title/ArabicUnicodeTest.php`
- [ ] T039 [P] [US3] Write Laravel integration tests for Title Check registration, configuration, and consumption flow in `tests/Integration/Laravel/TitleCheckRegistrationTest.php`

### Implementation for User Story 3

- [ ] T040 [US3] Implement `TitleScoreContributionBuilder` in `src/Checks/Title/Scoring/TitleScoreContributionBuilder.php` — assembles score contributions from rule outcomes with explicit rationale strings; maps rule findings to score impact values
- [ ] T041 [US3] Update `TitleCheck` in `src/Checks/Title/TitleCheck.php` — integrate score contribution assembly, metadata packaging (`TitleCheckMetadata`), and ensure deterministic ordering of all outputs in the `CheckOutcome`
- [ ] T042 [P] [US3] Implement `TitleCheckRegistration` in `src/Laravel/Support/TitleCheckRegistration.php` — thin adapter that resolves Laravel configuration values (thresholds, check class) and wires the Title Check into the existing MegSEO registration mechanism; contains no business logic
- [ ] T043 [P] [US3] Add Title Check class to `config/megseo.php` default `'checks'` array and wire title threshold keys into `TitleLengthPolicy` construction at the Laravel integration boundary

**Checkpoint**: All user stories should now be independently functional — the Title Check is complete, deterministic, and serves as the reference implementation

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Documentation, edge-case validation, and final quality assurance for the Title Check feature

- [ ] T044 [P] Update quickstart documentation in `specs/002-title-check/quickstart.md` with actual code examples and validated consumption patterns
- [ ] T045 [P] Validate public contract documentation in `specs/002-title-check/contracts/` against implemented behavior
- [ ] T046 Add edge-case regression tests — missing title with focus keyword, Arabic titles with duplicate support, empty keyword with valid title, zero-length boundaries — in `tests/Integration/Checks/Title/TitleCheckEdgeCasesTest.php`
- [ ] T047 [P] Verify backwards compatibility — existing core engine behavior is unchanged after Title Check registration; no existing test regressions
- [ ] T048 Run full feature-level API review and code cleanup across `src/Checks/Title/`
- [ ] T049 Validate that the feature can serve as the reference implementation pattern: verify rule composition style, normalization pipeline, metadata structure, and confidence signaling are consistent and comprehensible

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion — blocks all user stories
- **User Stories (Phases 3–5)**: Depend on Foundational completion
  - US1 (Phase 3) depends only on Phase 2
  - US2 (Phase 4) depends on Phase 2 + US1's `TitleCheck` structure (T029)
  - US3 (Phase 5) depends on Phase 2 + US1 + US2 structure
- **Polish (Phase 6)**: Depends on all user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Starts after Foundational. Delivers the MVP title presence/quality analysis.
- **User Story 2 (P2)**: Starts after US1's `TitleCheck` exists (T029). Depends on the normalization and rule execution pipeline established by US1.
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
Task: "Create SupportsDuplicateTitles contract in src/Checks/Title/Contracts/SupportsDuplicateTitles.php"
Task: "Create SupportsFocusKeyword contract in src/Checks/Title/Contracts/SupportsFocusKeyword.php"
Task: "Create TitleNormalizer contract in src/Checks/Title/Contracts/TitleNormalizer.php"

# Launch DTOs together:
Task: "Create TitleCheckInput DTO in src/Checks/Title/DTO/TitleCheckInput.php"
Task: "Create TitleNormalizationResult DTO in src/Checks/Title/DTO/TitleNormalizationResult.php"
Task: "Create TitleDuplicateMatch DTO in src/Checks/Title/DTO/TitleDuplicateMatch.php"
Task: "Create TitleCheckMetadata DTO in src/Checks/Title/DTO/TitleCheckMetadata.php"

# Launch support helpers together:
Task: "Implement TitleCharacterClassifier in src/Checks/Title/Support/TitleCharacterClassifier.php"
Task: "Implement TitleLengthPolicy in src/Checks/Title/Support/TitleLengthPolicy.php"
```

## Parallel Example: Phase 3 (User Story 1)

```bash
# Launch US1 rules together:
Task: "Implement DetectMissingTitle rule in src/Checks/Title/Rules/DetectMissingTitle.php"
Task: "Implement DetectEmptyTitle rule in src/Checks/Title/Rules/DetectEmptyTitle.php"
Task: "Implement DetectSeparatorOnlyTitle rule in src/Checks/Title/Rules/DetectSeparatorOnlyTitle.php"
Task: "Implement EvaluateTitleLength rule in src/Checks/Title/Rules/EvaluateTitleLength.php"

# Launch US1 tests together:
Task: "Write unit tests for DetectMissingTitle rule"
Task: "Write unit tests for DetectEmptyTitle rule"
Task: "Write unit tests for DetectSeparatorOnlyTitle rule"
Task: "Write unit tests for EvaluateTitleLength rule"
Task: "Write contract tests for Title Check API behavior"
Task: "Write integration tests for full US1 scenarios"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (contracts, DTOs, normalization, support)
3. Complete Phase 3: User Story 1 (title presence, emptiness, separator-only, length)
4. Run tests and validate the full US1 acceptance scenarios
5. Deploy as the first functional Title Check MVP

### Incremental Delivery

1. Complete Setup + Foundational to establish the feature shell and normalization pipeline
2. Add User Story 1 to deliver core title presence/quality analysis (issues + warnings)
3. Add User Story 2 to deliver search-relevant guidance (keyword suggestions + duplicate signals)
4. Add User Story 3 to lock down determinism, stable contracts, Arabic/Unicode, metadata, and Laravel integration
5. Finish with polish, edge-case regression coverage, and documentation validation

### Parallel Team Strategy

With multiple developers:

1. One developer establishes feature directories, contracts, and DTO scaffolding while another sets up test infrastructure and config stubs
2. After Foundational completes:
   - Developer A: User Story 1 rules and TitleCheck orchestration
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
- `TitleCheck` implements `MegSEO\Contracts\Check` with stable identifier `'seo.title'`
- All DTOs follow the existing `final readonly class` pattern from `src/DTO/`
- The feature reuses the core `CheckOutcome` DTO for results — no feature-specific result wrapper is introduced
- Normalization happens once before all rule evaluation — rules receive the normalized result
- Duplicate-title support is optional; the feature must degrade safely when data is absent
- The feature serves as the reference implementation — structure, naming, and composability matter for future check authors
- Arabic and Unicode are first-class concerns, not afterthoughts
- Deterministic outputs for identical inputs are non-negotiable
- Core feature classes under `src/Checks/Title/` must be framework-agnostic — never read Laravel config directly; configuration values are passed via constructors or method parameters at the integration boundary

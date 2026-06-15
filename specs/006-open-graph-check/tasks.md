# Tasks: Open Graph Check

**Input**: Design documents from `/specs/006-open-graph-check/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, quickstart.md

**Tests**: Tests are required by the MegSEO constitution and specification.

**Organization**: Tasks are grouped by user story for independent implementation and testing.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1, US2, US3)

## Path Conventions

- Source: `src/Checks/OpenGraph/`
- Tests: `tests/`
- Docs: `specs/006-open-graph-check/`

---

## Phase 1: Setup

- [x] T001 Create feature directory structure in `src/Checks/OpenGraph/Contracts/`, `src/Checks/OpenGraph/DTO/`, `src/Checks/OpenGraph/Rules/`, and `src/Checks/OpenGraph/Scoring/`
- [x] T002 [P] Create test directories in `tests/Unit/Checks/OpenGraph/`, `tests/Contract/Checks/OpenGraph/`, `tests/Integration/Checks/OpenGraph/`
- [x] T003 [P] Extend `config/megseo.php` with `open_graph` configuration section (optional toggles for strict mode)

---

## Phase 2: Foundational (Blocking Prerequisites)

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

### Contracts & DTOs

- [x] T004 [P] Create `OpenGraphDataProvider` contract in `src/Checks/OpenGraph/Contracts/OpenGraphDataProvider.php`
- [x] T005 [P] Create `OpenGraphCheckInput` DTO in `src/Checks/OpenGraph/DTO/OpenGraphCheckInput.php` — immutable, wraps og:title, og:description, og:image, all properties, image arrays
- [x] T006 [P] Create `OpenGraphPropertyReport` DTO in `src/Checks/OpenGraph/DTO/OpenGraphPropertyReport.php` — immutable, stores property name, status, value, message
- [x] T007 [P] Create `OpenGraphCheckMetadata` DTO in `src/Checks/OpenGraph/DTO/OpenGraphCheckMetadata.php` — immutable, stores check identifier, rule identifiers, provision flags, image quality flags, conflict flag

### Phase 2 Tests

- [x] T008 [P] Write unit tests for DTO immutability in `tests/Unit/Checks/OpenGraph/DtoImmutabilityTest.php`
- [x] T009 [P] Write contract tests for `OpenGraphDataProvider` in `tests/Contract/Checks/OpenGraph/OpenGraphContractsTest.php`

**Checkpoint**: Foundation ready

---

## Phase 3: User Story 1 — Validate Core OG Tags (Priority: P1) 🎯 MVP

### Tests

- [x] T010 [P] [US1] Write unit tests for `DetectMissingOgTitle` in `tests/Unit/Checks/OpenGraph/Rules/DetectMissingOgTitleTest.php`
- [x] T011 [P] [US1] Write unit tests for `DetectMissingOgDescription` in `tests/Unit/Checks/OpenGraph/Rules/DetectMissingOgDescriptionTest.php`
- [x] T012 [P] [US1] Write unit tests for `DetectMissingOgImage` in `tests/Unit/Checks/OpenGraph/Rules/DetectMissingOgImageTest.php`
- [x] T013 [P] [US1] Write unit tests for `DetectEmptyOgValues` in `tests/Unit/Checks/OpenGraph/Rules/DetectEmptyOgValuesTest.php`
- [x] T014 [P] [US1] Write contract tests for Open Graph Check API in `tests/Contract/Checks/OpenGraph/OpenGraphCheckContractTest.php`
- [x] T015 [P] [US1] Write integration tests for US1 scenarios in `tests/Integration/Checks/OpenGraph/OpenGraphCheckIntegrationTest.php`

### Implementation

- [x] T016 [P] [US1] Implement `DetectMissingOgTitle` in `src/Checks/OpenGraph/Rules/DetectMissingOgTitle.php` — produces an issue when og:title key is absent from input; does NOT fire when og:title is present but empty (empty handled by separate rule)
- [x] T017 [P] [US1] Implement `DetectMissingOgDescription` in `src/Checks/OpenGraph/Rules/DetectMissingOgDescription.php` — produces an issue when og:description key is absent; does NOT fire when present but empty
- [x] T018 [P] [US1] Implement `DetectMissingOgImage` in `src/Checks/OpenGraph/Rules/DetectMissingOgImage.php` — produces an issue when og:image key is absent; does NOT fire when present but empty
- [x] T019 [P] [US1] Implement `DetectEmptyOgValues` in `src/Checks/OpenGraph/Rules/DetectEmptyOgValues.php` — produces issues for empty/whitespace-only OG values; covers og:title, og:description, og:image; empty values are the root cause — empty takes precedence over missing
- [x] T020 [US1] Implement `OpenGraphCheck` in `src/Checks/OpenGraph/OpenGraphCheck.php` — orchestrates US1 rules; runs empty check first, then missing rules only for keys whose values are NOT empty (empty suppresses missing); stable identifier `seo.open_graph`

**Checkpoint**: US1 functional

---

## Phase 4: User Story 2 — OG Image Quality (Priority: P2)

### Tests

- [x] T021 [P] [US2] Write unit tests for `EvaluateOgImageUrl` in `tests/Unit/Checks/OpenGraph/Rules/EvaluateOgImageUrlTest.php`
- [x] T022 [P] [US2] Write unit tests for `DetectConflictingOgValues` in `tests/Unit/Checks/OpenGraph/Rules/DetectConflictingOgValuesTest.php`
- [x] T023 [P] [US2] Write integration tests for image quality + conflict scenarios in `tests/Integration/Checks/OpenGraph/OpenGraphCheckImageQualityTest.php`

### Implementation

- [x] T024 [P] [US2] Implement `EvaluateOgImageUrl` in `src/Checks/OpenGraph/Rules/EvaluateOgImageUrl.php` — validates absolute URL; flags relative URLs as warnings; reuses `CanonicalUrlValidator`
- [x] T025 [P] [US2] Implement `DetectConflictingOgValues` in `src/Checks/OpenGraph/Rules/DetectConflictingOgValues.php` — detects when the same OG property (title, description, or image) appears with multiple different values; identical duplicate values (e.g., `['Article', 'Article']`) are ignored — only value differences trigger conflicts
- [x] T026 [US2] Update `OpenGraphCheck` — integrate US2 rules

**Checkpoint**: US1+US2 functional

---

## Phase 5: User Story 3 — Reference Pattern + Scoring (Priority: P3)

### Tests

- [x] T027 [P] [US3] Write unit tests for `OpenGraphScoreContributionBuilder` in `tests/Unit/Checks/OpenGraph/ScoreContributionBuilderTest.php`
- [x] T028 [P] [US3] Write contract tests for determinism in `tests/Contract/Checks/OpenGraph/DeterminismContractTest.php`
- [x] T029 [P] [US3] Write integration tests for Unicode OG values in `tests/Integration/Checks/OpenGraph/UnicodeTest.php`
- [x] T030 [P] [US3] Write Laravel integration tests in `tests/Integration/Laravel/OpenGraphCheckRegistrationTest.php`

### Implementation

- [x] T031 [US3] Implement `OpenGraphScoreContributionBuilder` in `src/Checks/OpenGraph/Scoring/OpenGraphScoreContributionBuilder.php`
- [x] T032 [US3] Update `OpenGraphCheck` — integrate scoring and metadata
- [x] T033 [P] [US3] Implement `OpenGraphCheckRegistration` in `src/Laravel/Support/OpenGraphCheckRegistration.php` — thin adapter
- [x] T034 [P] [US3] Add Open Graph Check class to `config/megseo.php`

**Checkpoint**: US1+US2+US3 functional

---

## Phase 6: Polish

- [x] T035 [P] Update quickstart with code examples
- [x] T036 [P] Validate contract documentation
- [x] T037 Add edge-case regression tests in `tests/Integration/Checks/OpenGraph/OpenGraphCheckEdgeCasesTest.php`
- [x] T038 [P] Verify backward compatibility — no regressions
- [x] T039 Run API review and cleanup
- [x] T040 Validate reference implementation pattern consistency

---

## Dependencies & Execution Order

### Phase Dependencies

- Setup (1) → Foundational (2) → US1 (3) → US2 (4) → US3 (5) → Polish (6)

### Parallel Opportunities

- Phase 3: All tests (T010–T015) + all rules (T016–T019) can run in parallel
- Phase 4: Tests T021–T023 in parallel; rules T024–T025 in parallel
- Phase 5: Tests T027–T030 in parallel; Laravel tasks T033–T034 in parallel
- Phase 6: All polish tasks in parallel

---

## Notes

- `OpenGraphCheck` implements `MegSEO\Contracts\Check` with stable identifier `'seo.open_graph'`
- Reuses `MegSEO\Checks\Canonical\Support\CanonicalUrlValidator` for image URL validation
- No text normalization — OG values are opaque metadata strings
- Empty values take precedence over missing — a present-but-empty OG property produces only the empty finding, never the missing finding as well
- Duplicate identical values (e.g., `['Article', 'Article']`) are not considered conflicts — only genuinely different values trigger conflict findings
- Framework-agnostic core classes
- Deterministic outputs for identical inputs are non-negotiable

# Tasks: Hreflang Check

**Input**: Design documents from `/specs/008-hreflang-check/`

## Format: `[ID] [P?] [Story] Description`

## Path Conventions: Source `src/Checks/Hreflang/`, Tests `tests/`

---

## Phase 1: Setup

- [x] T001 Create feature directories in `src/Checks/Hreflang/{DTO,Rules,Scoring}/`
- [x] T002 [P] Create test directories in `tests/{Unit,Contract,Integration}/Checks/Hreflang/`
- [x] T003 [P] Extend `config/megseo.php` with `hreflang` config section

---

## Phase 2: Foundational (Blocking)

- [x] T004 [P] Create `HreflangCheckInput` DTO — array-of-entries input with per-entry access
- [x] T005 [P] Create `HreflangEntryReport` DTO
- [x] T006 [P] Create `HreflangCheckMetadata` DTO
- [x] T007 [P] Write DTO immutability tests
- [x] T008 [P] Write contract tests

---

## Phase 3: User Story 1 — Core Tags (P1) MVP

### Tests
- [x] T009 [P] [US1] Write tests for `DetectMissingHreflang` (empty array → issue)
- [x] T010 [P] [US1] Write tests for `ValidateHreflangLanguageCode` (regex: `/^[A-Za-z]{2,3}(?:-[A-Za-z0-9]{2,8})*$/` + `x-default`; skipped when value is empty)
- [x] T011 [P] [US1] Write tests for `ValidateHreflangUrl` (reuses CanonicalUrlValidator; skipped when href is empty)
- [x] T012 [P] [US1] Write tests for `DetectEmptyHreflangValues` — flags empty `hreflang` or `href` per entry; empty values suppress downstream language/URL validation for that entry
- [x] T013 [P] [US1] Write contract tests for Hreflang Check API
- [x] T014 [P] [US1] Write integration tests for US1

### Implementation
- [x] T015 [P] [US1] Implement `DetectMissingHreflang` — issue when entries array is empty or null
- [x] T016 [P] [US1] Implement `ValidateHreflangLanguageCode` — regex: `/^[A-Za-z]{2,3}(?:-[A-Za-z0-9]{2,8})*$/`, plus `x-default`; skipped when hreflang value is empty
- [x] T017 [P] [US1] Implement `ValidateHreflangUrl` — reuses `CanonicalUrlValidator`; flags relative/invalid URLs; skipped when href is empty
- [x] T018 [P] [US1] Implement `DetectEmptyHreflangValues` — flags empty `hreflang` or `href` per entry as issues; empty values suppress downstream validation for that entry
- [x] T019 [US1] Implement `HreflangCheck` — orchestrates US1 rules; stable identifier `seo.hreflang`

---

## Phase 4: User Story 2 — Quality (P2)

### Tests
- [x] T020 [P] [US2] Write tests for `DetectMissingXDefault` — suggestion only when 2+ language entries exist
- [x] T021 [P] [US2] Write tests for `EvaluateSelfReferencingHreflang` — only the entry matching the page language must self-reference; requires `page_url` and `page_language` from context
- [x] T022 [P] [US2] Write tests for `DetectConflictingHreflangEntries` — duplicate language codes → suggestion; same href for different language codes → warning
- [x] T023 [P] [US2] Write integration tests for quality scenarios

### Implementation
- [x] T024 [P] [US2] Implement `DetectMissingXDefault` — suggestion only when 2+ language entries exist
- [x] T025 [P] [US2] Implement `EvaluateSelfReferencingHreflang` — only the entry matching the page language must self-reference (href === page URL); requires `page_url` and `page_language` from context attributes
- [x] T026 [P] [US2] Implement `DetectConflictingHreflangEntries` — duplicate language codes → suggestion; same href for different language codes → warning
- [x] T027 [US2] Update `HreflangCheck` — integrate US2 rules

---

## Phase 5: User Story 3 — Scoring + Reference (P3)

- [x] T028 [P] [US3] Write tests for `HreflangScoreContributionBuilder`
- [x] T029 [P] [US3] Write determinism contract tests
- [x] T030 [P] [US3] Write IDN/Unicode integration tests
- [x] T031 [P] [US3] Write Laravel integration tests
- [x] T032 [US3] Implement `HreflangScoreContributionBuilder`
- [x] T033 [US3] Update `HreflangCheck` — scoring and metadata
- [x] T034 [P] [US3] Implement `HreflangCheckRegistration` — thin Laravel adapter
- [x] T035 [P] [US3] Add Hreflang Check to config

---

## Phase 6: Polish

- [x] T036 [P] Update quickstart
- [x] T037 [P] Validate contracts
- [x] T038 Add edge-case regression tests
- [x] T039 [P] Verify backward compatibility
- [x] T040 Run API review
- [x] T041 Validate reference pattern consistency

---

## Notes

- Identifier: `seo.hreflang`
- Input: Array of entries `[{hreflang, href}, ...]`
- Reuses `CanonicalUrlValidator` for href URL validation
- Language code regex: `/^[A-Za-z]{2,3}(?:-[A-Za-z0-9]{2,8})*$/` + `x-default`
- Empty values suppress downstream language/URL validation
- Self-referencing: only the entry matching `page_language` must self-reference via `page_url`
- x-default: suggestion only when 2+ language entries exist
- Conflicts: duplicate lang codes → suggestion; same-href different-lang → warning
- 7 rules: 4 per-entry + 3 cross-entry
- Framework-agnostic, deterministic

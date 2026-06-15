# Tasks: Twitter Card Check

**Input**: Design documents from `/specs/007-twitter-card-check/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, quickstart.md

## Format: `[ID] [P?] [Story] Description`

## Path Conventions
- Source: `src/Checks/TwitterCard/`
- Tests: `tests/`

---

## Phase 1: Setup

- [x] T001 Create feature directories in `src/Checks/TwitterCard/{Contracts,DTO,Rules,Scoring}/`
- [x] T002 [P] Create test directories in `tests/{Unit,Contract,Integration}/Checks/TwitterCard/`
- [x] T003 [P] Extend `config/megseo.php` with `twitter_card` configuration section

---

## Phase 2: Foundational (Blocking)

- [x] T004 [P] Create `TwitterCardDataProvider` contract
- [x] T005 [P] Create `TwitterCardCheckInput` DTO — structured array access, empty detection, array values
- [x] T006 [P] Create `TwitterCardPropertyReport` DTO
- [x] T007 [P] Create `TwitterCardCheckMetadata` DTO
- [x] T008 [P] Write DTO immutability tests
- [x] T009 [P] Write contract tests

---

## Phase 3: User Story 1 — Core Tags (P1) MVP

### Tests
- [x] T010 [P] [US1] Write tests for `DetectMissingTwitterCard`
- [x] T011 [P] [US1] Write tests for `DetectMissingTwitterTitle`
- [x] T012 [P] [US1] Write tests for `DetectMissingTwitterDescription`
- [x] T013 [P] [US1] Write tests for `DetectMissingTwitterImage`
- [x] T014 [P] [US1] Write tests for `DetectEmptyTwitterValues` (empty suppresses missing)
- [x] T015 [P] [US1] Write contract tests for Twitter Card Check API
- [x] T016 [P] [US1] Write integration tests for US1

### Implementation
- [x] T017 [P] [US1] Implement `DetectMissingTwitterCard` — issue when key absent; suppressed if empty
- [x] T018 [P] [US1] Implement `DetectMissingTwitterTitle` — same semantics
- [x] T019 [P] [US1] Implement `DetectMissingTwitterDescription` — same semantics
- [x] T020 [P] [US1] Implement `DetectMissingTwitterImage` — same semantics
- [x] T021 [P] [US1] Implement `DetectEmptyTwitterValues` — issues for empty/whitespace-only; covers all 4 required properties
- [x] T022 [US1] Implement `TwitterCardCheck` — orchestrates US1 rules; empty first, then missing for non-empty; stable identifier `seo.twitter_card`

---

## Phase 4: User Story 2 — Card Quality (P2)

### Tests
- [x] T023 [P] [US2] Write tests for `EvaluateTwitterCardType` (valid: summary/summary_large_image/app/player; invalid: anything else)
- [x] T024 [P] [US2] Write tests for `EvaluateTwitterImageUrl` (reuses CanonicalUrlValidator; absolute/relative/invalid/IDN)
- [x] T025 [P] [US2] Write tests for `DetectConflictingTwitterValues` (different values only; duplicate identical ignored)
- [x] T026 [P] [US2] Write integration tests for card type + image + conflicts

### Implementation
- [x] T027 [P] [US2] Implement `EvaluateTwitterCardType` — warns on unsupported card types
- [x] T028 [P] [US2] Implement `EvaluateTwitterImageUrl` — reuses `CanonicalUrlValidator`
- [x] T029 [P] [US2] Implement `DetectConflictingTwitterValues` — only different values trigger conflicts
- [x] T030 [US2] Update `TwitterCardCheck` — integrate US2 rules

---

## Phase 5: User Story 3 — Scoring + Reference Pattern (P3)

### Tests
- [x] T031 [P] [US3] Write tests for `TwitterCardScoreContributionBuilder`
- [x] T032 [P] [US3] Write determinism contract tests
- [x] T033 [P] [US3] Write Unicode integration tests
- [x] T034 [P] [US3] Write Laravel integration tests

### Implementation
- [x] T035 [US3] Implement `TwitterCardScoreContributionBuilder`
- [x] T036 [US3] Update `TwitterCardCheck` — scoring and metadata
- [x] T037 [P] [US3] Implement `TwitterCardCheckRegistration` — thin Laravel adapter
- [x] T038 [P] [US3] Add Twitter Card Check to `config/megseo.php`

---

## Phase 6: Polish

- [x] T039 [P] Update quickstart
- [x] T040 [P] Validate contracts
- [x] T041 Add edge-case regression tests
- [x] T042 [P] Verify backward compatibility
- [x] T043 Run API review
- [x] T044 Validate reference pattern consistency

---

## Notes

- `TwitterCardCheck` implements `MegSEO\Contracts\Check` with stable identifier `'seo.twitter_card'`
- Reuses `CanonicalUrlValidator` for twitter:image URL validation
- Empty values take precedence over missing
- Duplicate identical values are not conflicts
- 8 rules: 4 presence + 1 emptiness + 1 card type + 1 image + 1 conflicts
- Framework-agnostic core classes
- Deterministic outputs mandatory

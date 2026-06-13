# Tasks: MegSEO Core Analysis Engine

**Input**: Design documents from `/specs/001-core-analysis-engine/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Tests are required for this feature by the MegSEO constitution and specification.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- Single project package structure at repository root
- Source code in `src/`
- Tests in `tests/`
- Feature docs in `specs/001-core-analysis-engine/`

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Establish package skeleton, Composer metadata, and test tooling for the core analysis engine

- [x] T001 Create package directory structure in `src/Core`, `src/Contracts`, `src/DTO`, `src/Pipeline`, `src/Result`, `src/Policy`, `src/Laravel`, `src/Support`, `src/Exceptions`, `tests/Unit`, `tests/Contract`, and `tests/Integration`
- [x] T002 Create Composer package manifest and autoload configuration in `composer.json`
- [x] T003 [P] Configure PHPUnit or Pest test bootstrap for package, contract, and integration suites in `phpunit.xml` and `tests/`
- [x] T004 [P] Create Laravel package configuration stub in `src/Laravel/Configuration/megseo.php`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core contracts, shared DTO infrastructure, and deterministic engine primitives required by all user stories

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T005 Create core analyzer, pipeline, registry, aggregator, and policy contracts in `src/Contracts/`
- [x] T006 [P] Create immutable shared DTO base support and ordered collection helpers in `src/Support/ImmutableMap.php` and `src/Support/OrderedChecks.php`
- [x] T007 [P] Create core data DTOs for context, references, outcomes, score summaries, and execution decisions in `src/DTO/AnalysisContext.php`, `src/DTO/CheckReference.php`, `src/DTO/CheckOutcome.php`, `src/DTO/ScoreSummary.php`, and `src/DTO/ExecutionDecision.php`
- [x] T008 [P] Create result item DTOs in `src/DTO/AnalysisIssue.php`, `src/DTO/AnalysisWarning.php`, and `src/DTO/AnalysisSuggestion.php`
- [x] T009 Create the aggregated result DTO contract in `src/DTO/AnalysisResult.php`
- [x] T010 [P] Write unit tests for DTO immutability and deterministic ordering helpers in `tests/Unit/DTO/` and `tests/Unit/Support/`
- [x] T011 [P] Write contract tests for public analyzer, check, execution policy, and result contracts in `tests/Contract/`

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Run a Generic Analysis Session (Priority: P1) 🎯 MVP

**Goal**: Deliver a working generic analysis flow that accepts a context, runs independent checks through a deterministic pipeline, and returns one aggregated result

**Independent Test**: Execute analysis with stub checks and with zero registered checks, then verify one stable `AnalysisResult` is returned with deterministic score, issues, warnings, and suggestions accessors

### Tests for User Story 1

- [x] T012 [P] [US1] Create contract tests for the generic analysis API in `tests/Contract/AnalysisApiContractTest.php`
- [x] T013 [P] [US1] Create integration tests for deterministic multi-check and zero-check analysis flows in `tests/Integration/Core/GenericAnalysisSessionTest.php`

### Implementation for User Story 1

- [x] T014 [P] [US1] Implement deterministic check registry in `src/Pipeline/CheckRegistry.php`
- [x] T015 [P] [US1] Implement sequential execution plan and pipeline coordination in `src/Pipeline/SequentialExecutionPlan.php` and `src/Pipeline/CheckPipeline.php`
- [x] T016 [US1] Implement pipeline runner for immutable context execution in `src/Pipeline/PipelineRunner.php`
- [x] T017 [P] [US1] Implement score aggregation and result normalization in `src/Result/ScoreAggregator.php` and `src/Result/ResultNormalizer.php`
- [x] T018 [US1] Implement result aggregator in `src/Result/ResultAggregator.php`
- [x] T019 [US1] Implement generic engine entrypoint in `src/Core/Engine.php`
- [x] T020 [US1] Add stub-check integration fixtures for session testing in `tests/Fixtures/Core/`

**Checkpoint**: At this point, User Story 1 should be fully functional and testable independently

---

## Phase 4: User Story 2 - Extend the Engine with New Checks (Priority: P2)

**Goal**: Enable future feature modules to add checks and participate in analysis without modifying existing core behavior

**Independent Test**: Register a new stub check with a stable identifier through the extension mechanism and verify it executes alongside existing checks without changing prior engine behavior

### Tests for User Story 2

- [x] T021 [P] [US2] Create contract tests for check registration and stable identifier behavior in `tests/Contract/CheckRegistrationContractTest.php`
- [x] T022 [P] [US2] Create integration tests for feature-style check extension flows in `tests/Integration/Core/ExtensionMechanismTest.php`

### Implementation for User Story 2

- [x] T023 [P] [US2] Implement stable `Check` contract and registration interfaces in `src/Contracts/Check.php` and `src/Contracts/RegistersChecks.php`
- [x] T024 [P] [US2] Implement check result factory and context factory helpers in `src/Contracts/CheckResultFactory.php` and `src/Contracts/ContextFactory.php`
- [x] T025 [US2] Implement extension-oriented registration workflow in `src/Pipeline/CheckRegistry.php` and `src/DTO/CheckReference.php`
- [x] T026 [US2] Implement shared output contract adapter for downstream consumers in `src/Contracts/ArrayableResult.php`
- [x] T027 [US2] Document extension-safe stub check examples in `specs/001-core-analysis-engine/quickstart.md`

**Checkpoint**: At this point, User Stories 1 and 2 should both work independently

---

## Phase 5: User Story 3 - Rely on Stable and Testable Output Contracts (Priority: P3)

**Goal**: Provide stable output contracts, configurable execution policy behavior, and Laravel-facing integration that consumers can depend on safely

**Independent Test**: Repeat identical analysis runs under the same configuration and verify identical output contracts, then validate fail-fast and isolate-failures policies plus Laravel facade-based consumption

### Tests for User Story 3

- [x] T028 [P] [US3] Create contract tests for result contract stability and execution policy semantics in `tests/Contract/ResultContractTest.php` and `tests/Contract/ExecutionPolicyContractTest.php`
- [x] T029 [P] [US3] Create Laravel integration tests for service provider, facade binding, and config-driven execution policy in `tests/Integration/Laravel/MegSEOServiceProviderTest.php`

### Implementation for User Story 3

- [x] T030 [P] [US3] Implement execution policy contract and presets in `src/Contracts/ExecutionPolicy.php`, `src/Policy/FailFastExecutionPolicy.php`, `src/Policy/IsolateFailuresExecutionPolicy.php`, and `src/Policy/StandardExecutionPolicies.php`
- [x] T031 [US3] Integrate execution policy decisions into pipeline and aggregation flow in `src/Pipeline/PipelineRunner.php` and `src/Result/ResultAggregator.php`
- [x] T032 [P] [US3] Implement Laravel check registration bridge in `src/Laravel/Support/LaravelCheckRegistration.php`
- [x] T033 [P] [US3] Implement Laravel service provider and facade in `src/Laravel/Providers/MegSEOServiceProvider.php` and `src/Laravel/Facades/MegSEO.php`
- [x] T034 [P] [US3] Implement artisan analysis command in `src/Laravel/Console/AnalyzeContextCommand.php`
- [x] T035 [US3] Finalize stable `AnalysisResult` accessors and isolated failure metadata behavior in `src/DTO/AnalysisResult.php`

**Checkpoint**: All user stories should now be independently functional

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Final documentation, compatibility, and validation tasks affecting the whole foundation feature

- [ ] T036 [P] Update public contract documentation in `specs/001-core-analysis-engine/contracts/`
- [ ] T037 [P] Add backward compatibility and versioning notes to `specs/001-core-analysis-engine/plan.md` and `specs/001-core-analysis-engine/research.md`
- [ ] T038 Validate quickstart flows against the implemented package behavior in `specs/001-core-analysis-engine/quickstart.md`
- [ ] T039 [P] Add cross-cutting regression tests for deterministic repeated runs and empty-result scenarios in `tests/Integration/Core/DeterminismRegressionTest.php`
- [ ] T040 Run package-level cleanup and API review across `src/Core/`, `src/Contracts/`, `src/DTO/`, `src/Pipeline/`, `src/Result/`, `src/Policy/`, `src/Laravel/`, `src/Support/`, and `src/Exceptions/`

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - blocks all user stories
- **User Stories (Phases 3-5)**: Depend on Foundational completion
- **Polish (Phase 6)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Starts after Foundational completion and defines the MVP analysis flow
- **User Story 2 (P2)**: Starts after Foundational completion and depends on the existence of the generic pipeline seams delivered by US1, but remains independently testable through extension registration
- **User Story 3 (P3)**: Starts after Foundational completion and builds on the public contracts established by US1 and the extension seams validated by US2

### Within Each User Story

- Tests must be written and fail before implementation is considered complete
- Contracts and DTO seams before orchestration changes
- Registry and pipeline pieces before engine entrypoints
- Policy integration before Laravel adapter finalization
- Story validation before moving to the next dependent story

### Parallel Opportunities

- Phase 1 tasks marked `[P]` can run in parallel
- DTO and helper tasks in Phase 2 marked `[P]` can run in parallel after contracts begin
- US1 pipeline pieces `T014`, `T015`, and `T017` can run in parallel before aggregation wiring
- US2 contract/helper tasks `T023` and `T024` can run in parallel
- US3 policy and Laravel adapter tasks `T030`, `T032`, `T033`, and `T034` can run in parallel once result and pipeline seams are stable
- Polish documentation and regression tasks marked `[P]` can run in parallel

---

## Parallel Example: User Story 1

```bash
# Launch User Story 1 tests together:
Task: "Create contract tests for the generic analysis API in tests/Contract/AnalysisApiContractTest.php"
Task: "Create integration tests for deterministic multi-check and zero-check analysis flows in tests/Integration/Core/GenericAnalysisSessionTest.php"

# Launch User Story 1 pipeline building blocks together:
Task: "Implement deterministic check registry in src/Pipeline/CheckRegistry.php"
Task: "Implement sequential execution plan and pipeline coordination in src/Pipeline/SequentialExecutionPlan.php and src/Pipeline/CheckPipeline.php"
Task: "Implement score aggregation and result normalization in src/Result/ScoreAggregator.php and src/Result/ResultNormalizer.php"
```

---

## Parallel Example: User Story 3

```bash
# Launch User Story 3 adapter and policy tasks together:
Task: "Implement execution policy contract and presets in src/Contracts/ExecutionPolicy.php, src/Policy/FailFastExecutionPolicy.php, src/Policy/IsolateFailuresExecutionPolicy.php, and src/Policy/StandardExecutionPolicies.php"
Task: "Implement Laravel check registration bridge in src/Laravel/Support/LaravelCheckRegistration.php"
Task: "Implement Laravel service provider and facade in src/Laravel/Providers/MegSEOServiceProvider.php and src/Laravel/Facades/MegSEO.php"
Task: "Implement artisan analysis command in src/Laravel/Console/AnalyzeContextCommand.php"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational
3. Complete Phase 3: User Story 1
4. Validate deterministic multi-check and zero-check behavior
5. Review the generic `AnalysisResult` API before expanding to extension and Laravel concerns

### Incremental Delivery

1. Complete Setup + Foundational to establish stable contracts
2. Add User Story 1 to deliver the generic analysis session MVP
3. Add User Story 2 to make the engine safely extensible for future features
4. Add User Story 3 to lock down stable contracts, execution policy handling, and Laravel-first integration
5. Finish with polish, regression coverage, and documentation validation

### Parallel Team Strategy

With multiple developers:

1. One developer establishes package/test setup while another prepares DTO and contract scaffolding
2. After Foundational completes:
   - Developer A: User Story 1 pipeline and engine execution
   - Developer B: User Story 2 extension registration and result adapter seams
   - Developer C: User Story 3 execution policies and Laravel integration
3. Rejoin for regression testing, contract review, and documentation polish

---

## Notes

- `[P]` tasks touch different files and can be parallelized safely
- `[US1]`, `[US2]`, and `[US3]` map directly to the approved specification
- Every user story includes explicit tests because testing is mandatory for this feature
- Keep the core engine free of SEO-specific rules throughout implementation
- Preserve stable public APIs and identifiers as observable contracts

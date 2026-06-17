# Tasks: ADR-001 — Context Routing

**Input**: Design documents from `/specs/009-core-context-routing/`

## Format: `[ID] [P?] Description`

## Path Conventions: Core changes in `src/`, tests in `tests/`

---

## Phase 1: Core Implementation

- [x] T001 Add `inputs` parameter and `inputFor()`/`hasInputFor()`/`withSubject()` to `AnalysisContext` in `src/DTO/AnalysisContext.php`
- [x] T002 Enhance `PipelineRunner::run()` in `src/Pipeline/PipelineRunner.php` — resolve per-check input via `$context->inputFor($check->ref()->id)` before calling `$check->analyze()`; create derived context via `withSubject()` only when `$context->hasInputFor($check->ref()->id)` returns true (true mixed-mode: checks without explicit inputs receive the original context unchanged); preserve failure isolation, abort behavior, and existing pipeline semantics
- [x] T003 Verify legacy path untouched — when `inputs` is empty, inputFor returns `$this->subject` (identical to before) and no context derivation occurs

---

## Phase 2: Contract & Integration Tests

- [x] T004 [P] Write contract tests for `AnalysisContext` routing methods (`inputFor`, `hasInputFor`, `withSubject`) in `tests/Contract/AnalysisContextRoutingContractTest.php`
- [x] T005 [P] Write integration tests for routed single-check execution in `tests/Integration/Core/ContextRoutingTest.php`
- [x] T006 [P] Write integration tests for routed multi-check execution (all 6 checks)
- [x] T007 [P] Write integration tests for mixed routed + legacy execution
- [x] T008 [P] Write integration tests for determinism assurance (routed inputs produce identical outputs)
- [x] T009 [P] Write integration tests for `withSubject` immutability (original context unchanged after derivation)

---

## Phase 3: Regression & Validation

- [x] T010 Run full existing test suite — verify 568 tests pass unchanged
- [x] T011 Run v0.8 multi-check validation script — verify no regressions
- [x] T012 Verify backward compatibility: legacy `subject`-only usage produces identical results

---

## Notes

- Zero check modifications required
- Zero DTO/contract/feature modifications required outside AnalysisContext and PipelineRunner
- `AnalysisContext` remains `final readonly class`
- `PipelineRunner::run()` resolves per-check input; only checks with explicit routed inputs receive derived contexts
- Failure isolation, abort behavior, and execution ordering preserved through existing pipeline infrastructure
- Determinism preserved — `withSubject()` is pure (same inputs → same output)

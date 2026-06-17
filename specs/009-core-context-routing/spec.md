# Core Enhancement: ADR-001 — Shared AnalysisContext Subject Routing

**Branch**: `009-core-context-routing`  
**Created**: 2026-06-13  
**Status**: Draft  
**Identifier**: `core.context_routing`  
**Input**: Architectural Decision Record — address shared-context interference discovered during MegSEO v0.8 multi-check validation.

## Summary

MegSEO v0.8 validated six checks working independently, but multi-check execution revealed that different checks interpret `AnalysisContext::$subject` differently (strings, assoc arrays, indexed arrays). This produces interference when multiple checks run against a single shared subject. This ADR introduces a backward-compatible context-routing mechanism that enables true multi-check execution without breaking existing behavior.

## User Scenarios & Testing *(mandatory)*

### User Story 1 — Route Check-Specific Inputs (Priority: P1)

As a MegSEO developer running multiple checks simultaneously, I want to supply each check with its own input data through the context so that checks do not interfere with each other.

**Why this priority**: Multi-check execution is a core use case. Without routing, mixed input models interfere.

**Independent Test**: Create an AnalysisContext with routed inputs for all 6 checks, execute analysis, verify each check processes only its own data.

**Acceptance Scenarios**:

1. **Given** an `AnalysisContext` with `inputs` containing separate data for each check, **When** analysis runs with 6 checks, **Then** each check receives and processes only its routed input.
2. **Given** a check with a routed input, **When** the check calls `context->inputFor('seo.title')`, **Then** it receives the routed data for its identifier.
3. **Given** a check identifier with no routed input, **When** the check calls `context->hasInputFor('seo.unknown')`, **Then** it returns false.

---

### User Story 2 — Backward Compatibility With Legacy Subject (Priority: P2)

As an existing MegSEO user, I want my current code using `AnalysisContext(subject: ...)` to continue working without changes so that upgrading to the new version does not break my application.

**Why this priority**: Backward compatibility is mandatory. All existing tests and usage patterns must work unchanged.

**Independent Test**: Run the full existing test suite (568 tests) with the routing enhancement in place; verify zero regressions.

**Acceptance Scenarios**:

1. **Given** an `AnalysisContext` created with only a legacy `subject`, **When** any check calls `context->inputFor(...)`, **Then** it returns the legacy subject value.
2. **Given** the full existing test suite, **When** the routing enhancement is applied, **Then** all 568 tests pass without modification.

---

### User Story 3 — Mixed Routed and Legacy Inputs (Priority: P3)

As a developer transitioning to routed inputs, I want to supply routed data for some checks while other checks fall back to the legacy subject so that I can adopt routing incrementally.

**Why this priority**: Incremental adoption reduces migration risk. Some checks may receive routed data while others use the legacy subject.

**Independent Test**: Create a context with partial routed inputs and a legacy subject; verify that checks with routed data use it and checks without fall back to the subject.

**Acceptance Scenarios**:

1. **Given** a context with `inputs` for `seo.title` only and a legacy `subject`, **When** TitleCheck runs, **Then** it uses the routed title input.
2. **Given** a context with `inputs` for `seo.title` only and a legacy `subject`, **When** MetaDescriptionCheck runs, **Then** it uses the legacy subject (no routed input for its identifier).
3. **Given** a context with both inputs and subject, **When** a check resolves its input, **Then** it prefers the routed input over the legacy subject.

---

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: `AnalysisContext` MUST support an optional `inputs` parameter — an associative array mapping check identifiers to their input data.
- **FR-002**: `AnalysisContext` MUST expose `inputFor(string $identifier): mixed` — returns the routed input for a check identifier, or the legacy subject if no routed input exists.
- **FR-003**: `AnalysisContext` MUST expose `hasInputFor(string $identifier): bool` — returns true when a specific check has a routed input.
- **FR-004**: `Engine::analyze()` MUST resolve each check's input via `inputFor()` and create a derived context via `withSubject()` before passing it to the check. The original context MUST NOT be mutated.
- **FR-005**: Checks MUST access their input via `context->subject` (unchanged public property) as before.
- **FR-006**: `AnalysisContext` MUST expose `withSubject(mixed $subject): self` — returns a new instance with the given subject, preserving all attributes, options, inputs, and requestId from the original instance.
- **FR-007**: Legacy `AnalysisContext(subject: ...)` usage MUST continue to work identically.
- **FR-007**: Deterministic behavior MUST be preserved.
- **FR-008**: Existing feature tests MUST continue to pass unchanged. New routing tests may be added. Only AnalysisContext-specific expectations may evolve if required by the additive enhancement. No feature-level behavioral expectations may change.
- **FR-009**: The `inputs` array keys MUST be check identifiers.
- **FR-010**: The routing mechanism MUST be transparent to individual checks — each check reads `$context->subject` as before.
- **FR-011**: `AnalysisContext::withSubject()` MUST return a truly independent instance — no shared mutable state.

### Key Entities

- **AnalysisContext**: Enhanced with optional `inputs` map and `inputFor()`/`hasInputFor()` resolver methods.
- **Check Input**: Per-check data routed via the `inputs` map, keyed by check identifier.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: All 568 existing tests pass without modification after the enhancement is applied.
- **SC-002**: Multi-check execution via routed inputs produces zero interference between checks.
- **SC-003**: Full multi-check validation (6 checks simultaneously) succeeds with all 6 checks contributing correct scores.
- **SC-004**: Legacy `subject`-only execution produces identical results to pre-enhancement behavior.
- **SC-005**: Deterministic repeated runs with routed inputs produce identical outputs.

## Assumptions

- Each check receives a derived context via `withSubject()`, preserving the original context's immutability.
- The engine never mutates a context instance — it derives per-check contexts instead.
- Checks continue to read `$context->subject` as their input — no per-check code changes.
- `inputs` map keys correspond exactly to `CheckReference::$id` values.
- The legacy `subject` remains the fallback for checks without routed inputs.
- No new exceptions or error states are introduced — missing routed inputs silently fall back to subject.

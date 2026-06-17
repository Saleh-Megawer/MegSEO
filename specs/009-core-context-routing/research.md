# Research: ADR-001 Context Routing

## Decision 1: Additive over modification

**Decision**: Add `inputs` parameter, `inputFor()`, `hasInputFor()`, and `withSubject()` to AnalysisContext. The legacy subject path in Engine is preserved via an `inputs === []` guard.

**Rationale**: Existing code passes `subject` only. An empty `inputs` array triggers the identical legacy pipeline path — zero behavior change.

## Decision 2: Immutable context derivation via withSubject()

**Decision**: Engine creates per-check contexts via `$context->withSubject($resolved)` rather than mutating `$context->subject`.

**Rationale**: `AnalysisContext` is `final readonly`. Mutating would violate the immutability contract and could cause unexpected side effects if the context is reused. Derived instances make routing traceable and deterministic.

## Decision 3: Pipeline-level routing in PipelineRunner, not engine-level bypass

**Decision**: Enhance `PipelineRunner::run()` to resolve per-check inputs via `$context->inputFor()` and create derived contexts via `withSubject()`. The pipeline (CheckPipeline, SequentialExecutionPlan) remains the sole execution mechanism.

**Rationale**: The pipeline provides failure isolation, execution ordering, abort behavior, and future hooks. Bypassing it risks changing existing behavior. PipelineRunner already loops checks — adding input resolution there preserves all pipeline semantics.

**Alternatives considered**: Engine-level loop — rejected because it would duplicate failure handling and bypass pipeline extensibility.

## Decision 4: Prioritize routed input over legacy subject

**Decision**: `inputFor($id)` returns `$this->inputs[$id]` if present, else `$this->subject`.

**Rationale**: This enables incremental adoption — users can route some checks while others fall back. The explicit routed input takes precedence because it was intentionally provided.

## Decision 5: Preserve result aggregation unchanged

**Decision**: `ResultAggregator` receives outcomes from both routed and legacy paths identically. No changes to aggregation logic.

**Rationale**: Each check still returns a `CheckOutcome` with `CheckReference`. The aggregator already handles per-check outcomes generically.

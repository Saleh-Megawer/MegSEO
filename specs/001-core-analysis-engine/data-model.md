# Data Model: MegSEO Core Analysis Engine

## AnalysisContext

- **Purpose**: Immutable input contract for a single analysis session
- **Key fields**:
  - `subject`: normalized analysis payload supplied by the caller
  - `attributes`: generic key-value metadata for future features
  - `options`: engine-facing analysis options that do not embed SEO business rules
  - `requestId`: optional caller-provided trace identifier
- **Relationships**:
  - consumed by `Check`
  - passed unchanged through `Pipeline`
- **Validation rules**:
  - must be constructible without framework-specific state
  - must remain immutable after creation
  - must support deterministic serialization or inspection for tests and debugging

## CheckReference

- **Purpose**: Stable identity record for a registered check
- **Key fields**:
  - `id`: stable public identifier
  - `label`: human-readable name
  - `version`: optional check contract version or feature version marker
- **Relationships**:
  - attached to `Check`
  - referenced by `CheckOutcome`, isolated failures, and aggregated findings
- **Validation rules**:
  - `id` must be stable once public
  - `id` must be unique within a registry instance

## CheckOutcome

- **Purpose**: Immutable result emitted by a single check
- **Key fields**:
  - `check`: `CheckReference`
  - `scoreContribution`: optional score data
  - `issues`: list of issue DTOs
  - `warnings`: list of warning DTOs
  - `suggestions`: list of suggestion DTOs
  - `metadata`: optional generic per-check metadata
- **Relationships**:
  - produced by `Check`
  - consumed by `ResultAggregator`
- **Validation rules**:
  - each list defaults to empty, not null
  - content must not require SEO-specific assumptions in the core

## AnalysisIssue

- **Purpose**: Structured issue item in the final result contract
- **Key fields**:
  - `message`
  - `details`
  - `sourceCheckId`
  - `confidence`: optional confidence signal for future features
- **Relationships**:
  - may originate from one `CheckOutcome`
  - aggregated into `AnalysisResult`

## AnalysisWarning

- **Purpose**: Structured warning item in the final result contract
- **Key fields**:
  - `message`
  - `details`
  - `sourceCheckId`
- **Relationships**:
  - may originate from one `CheckOutcome`
  - aggregated into `AnalysisResult`

## AnalysisSuggestion

- **Purpose**: Structured suggestion item in the final result contract
- **Key fields**:
  - `message`
  - `details`
  - `sourceCheckId`
  - `confidence`: optional confidence signal for future features
- **Relationships**:
  - may originate from one `CheckOutcome`
  - aggregated into `AnalysisResult`

## ScoreSummary

- **Purpose**: Immutable score aggregate exposed by the final result
- **Key fields**:
  - `value`: optional numeric or normalized summary value
  - `contributors`: ordered list of score contributions
  - `metadata`: optional generic scoring metadata
- **Relationships**:
  - assembled from multiple `CheckOutcome` records
  - exposed by `AnalysisResult`
- **Validation rules**:
  - absence of scoring data must still yield a valid score summary contract

## ExecutionPolicy

- **Purpose**: Strategy object defining failure behavior during pipeline execution
- **Key fields**:
  - `mode`: fail-fast, isolate-failures, or future additive mode
  - `unsupportedBehavior`: decision for unsupported scenarios
  - `interruptionBehavior`: decision for interruptions
- **Relationships**:
  - consulted by `PipelineRunner`
  - may produce `ExecutionDecision` values
- **Validation rules**:
  - must be deterministic for identical inputs and failure events
  - must preserve the `AnalysisResult` contract when configured for isolation

## ExecutionDecision

- **Purpose**: Immutable policy response to a check failure or interruption event
- **Key fields**:
  - `action`: continue, isolate, abort
  - `reason`
  - `recordFailure`: boolean
- **Relationships**:
  - produced by `ExecutionPolicy`
  - consumed by `PipelineRunner` and `ResultAggregator`

## AnalysisResult

- **Purpose**: Final aggregated output of an analysis session
- **Key fields**:
  - `score`: `ScoreSummary`
  - `issues`: ordered list of `AnalysisIssue`
  - `warnings`: ordered list of `AnalysisWarning`
  - `suggestions`: ordered list of `AnalysisSuggestion`
  - `metadata`: generic result metadata
  - `failures`: optional isolated failure records when policy allows continuation
- **Relationships**:
  - assembled by `ResultAggregator`
  - returned by `MegSEOEngine`
- **Validation rules**:
  - must be valid for empty pipelines
  - accessor behavior must remain backward compatible once public
  - ordering must be deterministic

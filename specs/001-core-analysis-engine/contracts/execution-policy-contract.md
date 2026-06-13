# Contract: Execution Policy

## Purpose

Define how the engine reacts to check failures, interruptions, and unsupported scenarios without hardcoding that behavior in the pipeline.

## Required Responsibilities

- evaluate a failure or interruption event
- decide whether analysis continues, isolates the event, or aborts
- indicate whether failure information should be preserved in the final result

## Behavioral Guarantees

- policy decisions are deterministic for identical inputs and events
- isolate-failure policies preserve the overall `AnalysisResult` contract
- fail-fast policies stop analysis explicitly rather than through hidden side effects

## Compatibility Rules

- new policy modes should be additive
- changes to policy decision semantics must be documented because they affect observable engine behavior

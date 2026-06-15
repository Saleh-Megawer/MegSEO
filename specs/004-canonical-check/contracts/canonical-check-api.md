# Contract: Canonical Check API

## Purpose

Define how the Canonical Check participates in the existing MegSEO analysis flow.

## Behavioral Guarantees

- runs as a normal MegSEO check through the existing analysis engine
- emits deterministic output for identical inputs
- stays within canonical scope
- contributes issues, warnings, suggestions, score rationale, and stable metadata

## Consumer Expectations

- consumers continue using the standard MegSEO analysis entrypoint
- no HTTP requests or external services are required
- canonical findings are consumed through the existing result contract

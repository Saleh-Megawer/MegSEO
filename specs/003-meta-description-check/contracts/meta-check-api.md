# Contract: Meta Description Check API

## Purpose

Define how the Meta Description Check participates in the existing MegSEO analysis flow.

## Behavioral Guarantees

- runs as a normal MegSEO check through the existing analysis engine
- emits deterministic output for identical inputs
- stays within meta description scope
- contributes issues, warnings, suggestions, score rationale, and stable metadata

## Consumer Expectations

- consumers continue using the standard MegSEO analysis entrypoint
- Meta Description Check findings are consumed through the existing result contract
- no extra external service setup is required for core description analysis

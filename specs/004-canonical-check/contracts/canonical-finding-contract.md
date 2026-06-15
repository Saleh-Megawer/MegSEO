# Contract: Canonical Check Finding

## Purpose

Define the expected structure and behavior of findings emitted by Canonical Check.

## Behavioral Guarantees

- severe canonical problems produce issues (missing, empty, invalid, multiple)
- moderate concerns produce warnings (relative URLs)
- improvement opportunities produce suggestions (cross-domain, non-self-referencing)
- every meaningful score contribution includes rationale
- confidence is included only where it adds clarity
- emitted identifiers and metadata remain stable once public

## Compatibility Rules

- finding structure should remain additive where possible
- dashboard-oriented metadata should not be removed silently once released

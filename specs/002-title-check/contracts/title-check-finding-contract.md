# Contract: Title Check Finding

## Purpose

Define the expected structure and behavior of findings emitted by Title Check.

## Behavioral Guarantees

- severe title problems produce issues
- moderate title quality concerns produce warnings
- improvement opportunities produce suggestions
- every meaningful score contribution includes rationale
- confidence is included only where it adds clarity
- emitted identifiers and metadata remain stable once public

## Compatibility Rules

- finding structure should remain additive where possible
- dashboard-oriented metadata should not be removed silently once released

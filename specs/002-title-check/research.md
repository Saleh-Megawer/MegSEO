# Research: Title Check

## Decision 1: Normalize titles before every rule evaluation

**Decision**: Use one deterministic normalization pass to produce the canonical title representation used by all feature rules.

**Rationale**: This supports deterministic behavior, keeps repeated runs stable, and avoids inconsistent rule decisions caused by each rule normalizing text differently.

**Alternatives considered**:
- Per-rule normalization: rejected because it risks subtle inconsistencies
- No normalization: rejected because whitespace, Unicode, and separator handling would become unreliable

## Decision 2: Compose the check from small rule evaluators

**Decision**: Implement title analysis as coordinated rule objects for presence, emptiness, separator-only content, length, focus keyword presence, and duplicate-title support.

**Rationale**: This keeps the feature easier to read, test, and extend, and makes it a stronger reference implementation for future MegSEO checks.

**Alternatives considered**:
- One large check method: rejected because it is harder to maintain and less instructive for extension

## Decision 3: Keep thresholds configurable

**Decision**: Model recommended title ranges and short/long boundaries through feature-level configuration or policy objects.

**Rationale**: The constitution discourages hardcoded SEO heuristics when practical. Configurability also makes the feature easier to adapt over time.

**Alternatives considered**:
- Hardcoded numeric thresholds: rejected because they reduce flexibility and future maintainability

## Decision 4: Treat duplicate-title support as optional contextual capability

**Decision**: Support duplicate-title evaluation only when supporting data is available in the analysis context.

**Rationale**: This preserves progressive enhancement and avoids forcing storage, APIs, or external dependencies into the feature.

**Alternatives considered**:
- Require duplicate-title infrastructure always: rejected because it would undermine core usefulness and simplicity

## Decision 5: Use Unicode-aware and Arabic-safe text handling

**Decision**: Design normalization and text classification with Unicode-aware behavior and explicit Arabic compatibility from the start.

**Rationale**: Arabic is a strategic advantage for MegSEO, and multilingual-safe behavior is part of the product identity.

**Alternatives considered**:
- ASCII-first behavior with later multilingual patching: rejected because it would create architectural debt and lower-quality results

## Decision 6: Keep scoring secondary to findings

**Decision**: Emit score contributions with rationale, but treat issues, warnings, and suggestions as the primary user-facing outputs.

**Rationale**: This aligns the feature with MegSEO’s action-oriented product philosophy and avoids turning Title Check into a vanity score mechanism.

**Alternatives considered**:
- Score-led output: rejected because it weakens explanation quality and educational value

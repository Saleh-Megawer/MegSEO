# Implementation Plan: Open Graph Check

**Branch**: `006-open-graph-check` | **Date**: 2026-06-13 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/006-open-graph-check/spec.md`

## Summary

Implement the fourth MegSEO feature as an Open Graph Check that evaluates og:title, og:description, and og:image for presence and validity, and returns deterministic findings. This is the first social metadata check, reusing the URL validation infrastructure from Canonical Check while following the established `src/Checks/` architecture.

## Technical Context

**Language/Version**: PHP 8.2+  
**Primary Dependencies**: Existing MegSEO core engine, `CanonicalUrlValidator` for og:image URL validation, Composer, Laravel integration  
**Testing**: Pest for unit, contract, and Laravel integration  
**Constraints**: No HTTP requests, no image downloads, deterministic outputs, stable contracts  
**Scale/Scope**: One production-ready social metadata feature module

## Constitution Check

- **Architecture Before Features**: Pass. Follows approved specification.
- **Feature-Driven Development**: Pass. Scoped to `src/Checks/OpenGraph/`.
- **Generic Core, Domain-Specific Features**: Pass. All OG logic inside feature.
- **Open for Extension**: Pass. Reuses `CanonicalUrlValidator` — cross-feature composition.
- **Quality, Documentation, and Stability**: Pass.
- **Actionable, Educational Output**: Pass.
- **Arabic and Future Language Support**: Pass. Unicode values supported.
- **Laravel-First Developer Experience**: Pass.
- **Determinism and Human Override**: Pass.

## Project Structure

```text
src/
├── Checks/
│   └── OpenGraph/
│       ├── Contracts/
│       │   └── OpenGraphDataProvider.php
│       ├── DTO/
│       │   ├── OpenGraphCheckInput.php
│       │   ├── OpenGraphCheckMetadata.php
│       │   └── OpenGraphPropertyReport.php
│       ├── Rules/
│       │   ├── DetectMissingOgTitle.php
│       │   ├── DetectMissingOgDescription.php
│       │   ├── DetectMissingOgImage.php
│       │   ├── DetectEmptyOgValues.php
│       │   ├── EvaluateOgImageUrl.php
│       │   └── DetectConflictingOgValues.php
│       ├── Scoring/
│       │   └── OpenGraphScoreContributionBuilder.php
│       └── OpenGraphCheck.php
├── Laravel/
│   └── Support/
│       └── OpenGraphCheckRegistration.php

tests/
├── Unit/Checks/OpenGraph/
├── Contract/Checks/OpenGraph/
└── Integration/Checks/OpenGraph/
```

## Phase 0: Research

### Key Decisions

- Reuse `CanonicalUrlValidator` for og:image URL validation (FR-017).
- Model as 6 rule evaluators: 3 for presence (title/desc/image), 1 for emptiness, 1 for image quality, 1 for conflicts.
- OG data arrives as structured key-value input (array of OG properties).
- No text normalization needed — OG values are opaque strings, not analyzed for content.

### Alternatives Considered

- **Dedicated image validator**: Rejected — `CanonicalUrlValidator` already handles URL validation correctly.
- **Text normalization for OG values**: Rejected — OG values are compared for presence/emptiness only.

## Phase 1: Design

### Core Components

- `OpenGraphCheck`: Orchestrator. Identifier: `seo.open_graph`.
- 6 rule evaluators
- `OpenGraphScoreContributionBuilder`: Scoring with rationale
- `OpenGraphCheckRegistration`: Thin Laravel adapter

### Scoring

| Finding | Deduction |
|---------|-----------|
| Missing og:title | -25 |
| Missing og:description | -25 |
| Missing og:image | -25 |
| Empty OG value | -20 |
| Invalid og:image URL | -15 |
| Relative og:image URL | -10 |
| Conflicting values | -15 |
| All clean | 0 (score: 100) |

### Pipeline Execution

1. Parse OG input structure
2. Evaluate presence rules (title, description, image)
3. Evaluate emptiness rule
4. Evaluate image quality (validity, relativity)
5. Evaluate conflicts
6. Assemble score and metadata

## Phase 2: Implementation Strategy

1. Feature shell under `src/Checks/OpenGraph/`
2. DTOs and contracts
3. Rules: presence → emptiness → image → conflicts
4. Scoring and metadata
5. Laravel integration
6. Tests across all phases

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| None | N/A | N/A |

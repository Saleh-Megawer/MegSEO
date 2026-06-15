# Implementation Plan: Twitter Card Check

**Branch**: `007-twitter-card-check` | **Date**: 2026-06-13 | **Spec**: [spec.md](./spec.md)

## Summary

Implement the fifth MegSEO feature as a Twitter Card Check. Mirrors Open Graph Check architecture with one addition: card type validation. Reuses `CanonicalUrlValidator` for image URLs. Follows the established `src/Checks/` pattern.

## Technical Context

**Language/Version**: PHP 8.2+  
**Dependencies**: MegSEO Core, `CanonicalUrlValidator`, Composer, Laravel integration  
**Testing**: Pest  
**Constraints**: No HTTP requests, deterministic outputs, stable contracts

## Constitution Check

- All 9 gates pass — follows approved spec, scoped to `src/Checks/TwitterCard/`, reuses cross-feature validators.

## Project Structure

```text
src/Checks/TwitterCard/
├── Contracts/
│   └── TwitterCardDataProvider.php
├── DTO/
│   ├── TwitterCardCheckInput.php
│   ├── TwitterCardCheckMetadata.php
│   └── TwitterCardPropertyReport.php
├── Rules/
│   ├── DetectMissingTwitterCard.php
│   ├── DetectMissingTwitterTitle.php
│   ├── DetectMissingTwitterDescription.php
│   ├── DetectMissingTwitterImage.php
│   ├── DetectEmptyTwitterValues.php
│   ├── EvaluateTwitterCardType.php
│   ├── EvaluateTwitterImageUrl.php
│   └── DetectConflictingTwitterValues.php
├── Scoring/
│   └── TwitterCardScoreContributionBuilder.php
└── TwitterCardCheck.php
```

## Phase 0: Research

### Key Decisions

- Mirror Open Graph Check architecture exactly.
- Add card type validation (summary, summary_large_image, app, player) as the unique differentiator.
- Reuse `CanonicalUrlValidator` for image URLs.
- Same empty-suppresses-missing and duplicate-values-not-conflicts rules as OG.

## Phase 1: Design

### Core Components

- `TwitterCardCheck`: Orchestrator. Identifier: `seo.twitter_card`.
- 8 rule evaluators (4 presence + 1 emptiness + 1 card type + 1 image + 1 conflicts)
- `TwitterCardScoreContributionBuilder`
- `TwitterCardCheckRegistration`: Thin Laravel adapter

### Scoring

| Finding | Deduction |
|---------|-----------|
| Missing twitter:card/title/desc/image | -20 each |
| Empty Twitter value | -15 |
| Invalid card type | -10 |
| Invalid image URL | -15 |
| Relative image URL | -10 |
| Conflicting values | -15 |

### Pipeline

1. Parse structured input
2. Empty check (suppresses missing)
3. Presence checks (card, title, desc, image)
4. Card type validation
5. Image URL validation
6. Conflict detection
7. Score assembly

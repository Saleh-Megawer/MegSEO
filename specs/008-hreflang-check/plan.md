# Implementation Plan: Hreflang Check

**Branch**: `008-hreflang-check` | **Date**: 2026-06-13 | **Spec**: [spec.md](./spec.md)

## Summary

Implement the sixth MegSEO feature as a Hreflang Check — the first internationalization SEO check. Validates hreflang annotations for language code correctness, URL validity, self-referencing, x-default presence, and conflicts. Reuses `CanonicalUrlValidator` for href URLs.

## Technical Context

**Language/Version**: PHP 8.2+  
**Dependencies**: MegSEO Core, `CanonicalUrlValidator`, Composer, Laravel  
**Testing**: Pest  
**Constraints**: No HTTP requests, deterministic, BCP 47 language code validation

## Constitution Check

All 9 gates pass — follows approved spec, scoped to `src/Checks/Hreflang/`, reuses cross-feature validators.

## Project Structure

```text
src/Checks/Hreflang/
├── DTO/
│   ├── HreflangCheckInput.php
│   ├── HreflangCheckMetadata.php
│   └── HreflangEntryReport.php
├── Rules/
│   ├── DetectMissingHreflang.php
│   ├── ValidateHreflangLanguageCode.php
│   ├── ValidateHreflangUrl.php
│   ├── DetectEmptyHreflangValues.php
│   ├── DetectMissingXDefault.php
│   ├── EvaluateSelfReferencingHreflang.php
│   └── DetectConflictingHreflangEntries.php
├── Scoring/
│   └── HreflangScoreContributionBuilder.php
└── HreflangCheck.php
```

## Phase 0: Research

### Key Decisions

- **Language code validation**: Match against `/^[A-Za-z]{2,3}(?:-[A-Za-z0-9]{2,8})*$/` plus `x-default`.
- **Array-based input**: Each hreflang entry is `{hreflang: string, href: string}`. Input is an array of entries.
- **Reuse CanonicalUrlValidator**: For href URL validation.
- **7 rule evaluators**: Missing, empty values, language code, URL, x-default, self-referencing, conflicts.
- **Self-referencing**: Only the entry matching the current page language must self-reference the current page URL.
- **x-default**: Suggestion only when 2+ language entries exist.
- **Conflicts**: Duplicate language codes → suggestion. Same href for different lang codes → warning.

### Rationale

- `/^[A-Za-z]{2,3}(?:-[A-Za-z0-9]{2,8})*$/` covers common hreflang usage (e.g., `en`, `en-US`, `zh-Hans`).
- Multi-entry arrays model hreflang sets accurately.
- CanonicalUrlValidator is proven across 3 previous checks.
- Language-specific self-referencing is the search engine expectation — only the current page's language entry should self-reference.
- x-default without multiple languages is meaningless.

## Phase 1: Design

### Core Components

- `HreflangCheck`: Orchestrator. Identifier: `seo.hreflang`.
- 7 rule evaluators
- `HreflangScoreContributionBuilder`
- `HreflangCheckRegistration`: Thin Laravel adapter

### Scoring

| Finding | Deduction |
|---------|-----------|
| Missing hreflang entirely | -30 |
| Empty href/hreflang value | -15 |
| Invalid language code | -10 |
| Relative/invalid href URL | -15 |
| Missing x-default (2+ entries) | -10 |
| Not self-referencing (page-language entry) | -5 |
| Duplicate language codes (suggestion) | -10 |
| Same href different lang codes (warning) | -10 |

### Pipeline

1. Parse hreflang entries array
2. Missing check (empty array)
3. Per-entry (skipped if value is empty): empty detection, language code, URL
4. Cross-entry: x-default (only if 2+ entries), self-referencing (only for current-page-language entry), conflicts (duplicate lang → suggestion, same-href different-lang → warning)
5. Score assembly

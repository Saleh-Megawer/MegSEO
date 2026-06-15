# Feature Specification: Canonical Check

**Feature Branch**: `004-canonical-check`  
**Created**: 2026-06-13  
**Status**: Draft  
**Input**: User description: "Create the next MegSEO feature: Canonical Check. Analyze canonical tags and provide actionable technical SEO recommendations."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Analyze a Single Canonical Tag Safely (Priority: P1)

As a Laravel developer using MegSEO, I want the Canonical Check to evaluate whether a canonical tag is present, valid, and properly formed so that I can quickly identify severe canonical problems that may confuse search engines.

**Why this priority**: Missing or broken canonical tags can cause duplicate content issues, diluted ranking signals, and incorrect page indexing. Reliable canonical validation is foundational.

**Independent Test**: Can be fully tested by submitting content contexts with valid, missing, empty, invalid, and multiple canonical tags and verifying that the check returns the correct issues.

**Acceptance Scenarios**:

1. **Given** a valid self-referencing absolute canonical URL, **When** the Canonical Check runs, **Then** it returns no issues or warnings.
2. **Given** missing canonical data, **When** the Canonical Check runs, **Then** it returns an issue describing the missing canonical tag and handles the input safely.
3. **Given** an empty canonical value, **When** the Canonical Check runs, **Then** it returns an issue indicating that the canonical is empty.
4. **Given** an invalid canonical URL (malformed, non-http, or non-absolute), **When** the Canonical Check runs, **Then** it returns an issue describing the validation failure.
5. **Given** multiple canonical tags detected, **When** the Canonical Check runs, **Then** it returns an issue indicating the conflict.

---

### User Story 2 - Provide Canonical Quality Guidance (Priority: P2)

As a developer or site maintainer, I want the Canonical Check to provide meaningful guidance about self-referencing behavior, relative URLs, and cross-domain canonical patterns so that I can improve canonical implementation quality.

**Why this priority**: After basic validity, canonical quality guidance helps teams avoid subtle SEO problems like relative canonicals or unintended cross-domain references.

**Independent Test**: Can be fully tested by running the Canonical Check with relative URLs, self-referencing patterns, cross-domain canonicals, and normalized URL comparison scenarios.

**Acceptance Scenarios**:

1. **Given** a relative canonical URL, **When** the Canonical Check runs, **Then** it returns a warning or suggestion recommending absolute URLs.
2. **Given** a canonical URL that differs from the page URL, **When** the Canonical Check runs, **Then** it returns a suggestion acknowledging the deliberate cross-referencing.
3. **Given** a canonical URL pointing to a different domain, **When** the Canonical Check runs, **Then** it returns a suggestion flagging the cross-domain canonical.
4. **Given** URLs that differ only in normalization (trailing slash, protocol, www prefix), **When** the Canonical Check compares them, **Then** it recognizes them as equivalent after normalization.

---

### User Story 3 - Act as the Reference Technical SEO Check Pattern (Priority: P3)

As a third-party MegSEO feature author, I want the Canonical Check to follow the same architectural patterns as Title Check and Meta Description Check — stable identifiers, score contribution rationale, confidence handling, metadata usage, and deterministic behavior — so that I can reference it alongside existing features when designing technical SEO checks.

**Why this priority**: MegSEO needs a reference implementation for technical SEO checks. Canonical Check is the first technical check and must demonstrate how URL-based checks differ from text-based checks while maintaining architectural consistency.

**Independent Test**: Can be fully tested by verifying that repeated runs with identical inputs produce identical outputs, that public result contracts remain consistent with existing checks, and that URL normalization is deterministic.

**Acceptance Scenarios**:

1. **Given** identical canonical input, page URL input, and configuration, **When** the Canonical Check runs repeatedly, **Then** it produces identical findings, metadata, and score contribution rationale.
2. **Given** URLs with Unicode characters or international domain names, **When** the Canonical Check runs, **Then** the URLs are normalized and analyzed correctly.
3. **Given** dashboard or reporting consumers rely on stable check identifiers and metadata, **When** the Canonical Check emits findings, **Then** the identifiers and metadata remain stable.

---

### Edge Cases

- What happens when canonical data is completely missing from the analysis context?
- How does the check behave when an empty string is provided as the canonical URL?
- What happens when the canonical URL is not a valid URL (e.g., `not-a-url`, `javascript:void(0)`)?
- How does the check handle multiple canonical tags with conflicting values?
- What happens when the canonical URL is identical to the page URL except for trailing slash?
- How does the check handle canonical URLs with protocol differences (http vs https)?
- How does the check handle canonical URLs with www vs non-www differences?
- What happens when the canonical URL is a relative path like `/page`?
- How does the check handle canonical URLs containing Unicode or IDN characters?
- How does the check behave when the canonical URL is excessively long?
- How does the check avoid nondeterministic outcomes when repeated with the same input and support data?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The system MUST analyze canonical tag existence and identify when canonical data is missing.
- **FR-002**: The system MUST identify empty canonical values as unusable.
- **FR-003**: The system MUST validate that canonical values are well-formed absolute URLs.
- **FR-004**: The system MUST detect and report when multiple canonical tags are present.
- **FR-005**: The system MUST normalize canonical URLs before comparison (strip trailing slashes, normalize protocols, normalize hostnames).
- **FR-006**: The system MUST compare the normalized canonical URL against the supplied page URL to detect self-referencing patterns.
- **FR-007**: The system MUST identify relative canonical URLs and recommend absolute URLs.
- **FR-008**: The system MUST detect cross-domain canonicals and provide appropriate guidance.
- **FR-009**: The system MUST provide issues for severe canonical problems.
- **FR-010**: The system MUST provide warnings for moderate canonical concerns.
- **FR-011**: The system MUST provide suggestions for canonical improvements.
- **FR-012**: The system MUST provide score contributions with clear rationale tied to canonical findings.
- **FR-013**: The system MUST provide confidence values where confidence signaling is appropriate.
- **FR-014**: The system MUST emit stable identifiers and metadata suitable for dashboards.
- **FR-015**: The system MUST handle missing canonical data safely without crashing.
- **FR-016**: The system MUST keep all analysis deterministic for identical inputs.
- **FR-017**: The system MUST remain within canonical scope and MUST NOT include HTTP requests, crawling, sitemap validation, robots.txt analysis, external services, or cross-page indexing verification.
- **FR-018**: The system MUST support URL normalization that handles trailing slashes, protocol differences, hostname normalization, and percent-encoding.
- **FR-019**: The system MUST support Unicode and IDN (Internationalized Domain Name) characters in canonical URLs.
- **FR-020**: The system MUST follow the same architectural patterns as Title Check and Meta Description Check — feature-scoped module under `src/Checks/`, composition of small rule evaluators, deterministic normalization, explicit score rationale, stable metadata, and graceful degradation.

### Key Entities *(include if feature involves data)*

- **Canonical Input**: The canonical value and optional page URL supplied for analysis, including cases where the value is missing, empty, or malformed.
- **Normalized Canonical URL**: The canonical URL after deterministic normalization — normalized protocol, hostname, path (trailing slash stripped), and query string ordering.
- **Page URL**: The target page URL supplied for comparison against the canonical URL.
- **URL Normalization Result**: The result of normalizing both the canonical and page URLs, including flags indicating what transformations were applied.
- **Canonical Check Finding**: A structured output item representing an issue, warning, or suggestion produced by the check, including rationale, confidence where appropriate, and stable metadata.
- **Canonical Score Contribution**: The structured score impact emitted by the check together with an explanation of why the canonical quality affected the score.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: In validation scenarios covering valid, missing, empty, invalid, multiple, relative, cross-domain, and self-referencing canonical URLs, the Canonical Check returns the expected finding category in each case.
- **SC-002**: Repeated Canonical Check runs with identical canonical input, page URL input, and configuration produce identical outputs in 100% of validation runs.
- **SC-003**: The feature follows the same architectural patterns as Title Check and Meta Description Check by demonstrating stable identifiers, score contribution rationale, confidence signaling, and structured metadata.
- **SC-004**: Developers can understand and extend the feature using only the public contracts, documentation, and platform conventions.
- **SC-005**: The feature preserves stable public contracts so downstream consumers can continue using findings, score contributions, and metadata without unexpected structural changes.
- **SC-006**: The developer experience for running and consuming the Canonical Check remains consistent with the established MegSEO platform and existing check conventions.

## Assumptions

- The MegSEO core engine and check integration foundation already exist and are available for feature-level checks.
- Title Check and Meta Description Check serve as the established reference implementations; Canonical Check reuses their patterns.
- URL normalization follows standard practices: lowercase scheme and hostname, strip default ports, remove trailing slashes, sort query parameters, decode safe percent-encoded characters.
- The page URL is supplied via the analysis context alongside the canonical URL.
- Canonical validation does not make HTTP requests to verify URL accessibility — it validates syntax and structure only.
- Multiple canonical detection depends on how the input data exposes canonical values (single value vs array).
- The Canonical Check uses the stable identifier `seo.canonical`.
- The feature lives under `src/Checks/Canonical/` following the established convention.
- Cross-domain canonicals are flagged as suggestions, not issues — they may be intentional for syndicated content.

# Feature Specification: Hreflang Check

**Feature Branch**: `008-hreflang-check`  
**Created**: 2026-06-13  
**Status**: Draft  
**Input**: User description: "Analyze hreflang tags and provide actionable international SEO recommendations."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Validate Core Hreflang Tags (Priority: P1)

As a Laravel developer managing a multilingual website, I want MegSEO to validate hreflang annotations so that search engines correctly identify language and regional targeting.

**Why this priority**: Hreflang errors can cause incorrect language serving in search results, directly harming international traffic.

**Independent Test**: Submit hreflang data with valid, missing, and malformed entries; verify correct findings.

**Acceptance Scenarios**:

1. **Given** a valid self-referencing hreflang with correct language code and absolute URL, **When** the check runs, **Then** it returns no issues.
2. **Given** missing hreflang data entirely, **When** the check runs, **Then** it returns an issue.
3. **Given** an hreflang entry with an invalid language code, **When** the check runs, **Then** it returns a warning.
4. **Given** an hreflang entry with a relative URL, **When** the check runs, **Then** it returns a warning.
5. **Given** an hreflang entry with an empty href value, **When** the check runs, **Then** it returns an issue.

---

### User Story 2 - Validate Hreflang Quality (Priority: P2)

As a developer, I want the check to detect missing x-default, non-self-referencing tags, and conflicts so that I can ensure complete hreflang coverage.

**Why this priority**: x-default is critical for language fallback. Self-referencing and conflict detection prevent canonical confusion.

**Independent Test**: Submit hreflang data with and without x-default, self-referencing patterns, and conflicts; verify findings.

**Acceptance Scenarios**:

1. **Given** hreflang data without an x-default entry, **When** the check runs, **Then** it returns a suggestion.
2. **Given** hreflang data where one entry does not self-reference (href != page URL), **When** the check runs, **Then** it returns a suggestion.
3. **Given** multiple entries for the same language code, **When** the check runs, **Then** it returns a suggestion.
4. **Given** conflicting hreflang values (same href, different lang), **When** the check runs, **Then** it returns a finding.

---

### User Story 3 - Act as the Internationalization Reference Pattern (Priority: P3)

As a third-party feature author, I want the Hreflang Check to follow the same architectural patterns so that internationalization checks are consistent.

**Why this priority**: MegSEO needs a reference implementation for international SEO checks. Hreflang is the first of this category.

**Independent Test**: Verify deterministic output, stable identifiers, metadata consistency.

**Acceptance Scenarios**:

1. **Given** identical hreflang input, **When** the check runs repeatedly, **Then** it produces identical outputs.
2. **Given** hreflang entries with IDN/Unicode URLs, **When** the check runs, **Then** URLs are validated correctly.
3. **Given** dashboard consumers, **When** the check emits findings, **Then** identifiers and metadata remain stable.

---

### Edge Cases

- What happens when hreflang data is completely missing?
- How does the check handle an empty array vs null?
- What happens with empty string values for href or hreflang?
- How does the check handle malformed language codes?
- What happens with relative URLs in href?
- How does the check handle multiple entries for the same language?
- What happens when the page URL is missing (self-referencing not possible)?
- How does the check handle IDN domains in hreflang URLs?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST evaluate hreflang presence and flag missing hreflang data.
- **FR-002**: System MUST validate hreflang language codes against BCP 47 format.
- **FR-003**: System MUST validate hreflang href URLs as absolute, valid URLs.
- **FR-004**: System MUST detect empty href or hreflang values.
- **FR-005**: System MUST detect missing x-default entry.
- **FR-006**: System MUST detect non-self-referencing hreflang entries.
- **FR-007**: System MUST detect duplicate entries for the same language.
- **FR-008**: System MUST provide issues for severe problems.
- **FR-009**: System MUST provide warnings for moderate concerns.
- **FR-010**: System MUST provide suggestions for improvements.
- **FR-011**: System MUST provide score contributions with rationale.
- **FR-012**: System MUST emit stable identifiers and metadata.
- **FR-013**: System MUST keep analysis deterministic.
- **FR-014**: No HTTP requests, crawling, or external services.
- **FR-015**: System MUST reuse CanonicalUrlValidator for href URL validation.
- **FR-016**: System MUST support IDN/Unicode URLs.
- **FR-017**: System MUST follow the same architectural patterns.

### Key Entities

- **Hreflang Entry**: One hreflang annotation with `hreflang` code and `href` URL.
- **Hreflang Input**: An array of hreflang entries supplied for analysis.
- **Hreflang Finding**: Issue, warning, or suggestion about a specific hreflang entry or the overall set.

## Success Criteria *(mandatory)*

- **SC-001**: In validation scenarios covering valid, missing, invalid language, empty, relative URL, no x-default, and conflict states, the check returns expected findings.
- **SC-002**: Repeated runs produce identical outputs in 100% of runs.
- **SC-003**: Follows the same architectural patterns as previous checks.
- **SC-004**: Existing Core and all feature check behavior remain unchanged.

## Assumptions

- MegSEO core and previous checks exist and are stable.
- `CanonicalUrlValidator` is reused for href URL validation.
- Hreflang data arrives as an array of entries, each with `hreflang` and `href` keys.
- Language code validation uses BCP 47 (`/^[a-z]{2}(-[A-Z]{2})?$/`).
- The page URL is supplied via context attributes for self-referencing detection.
- The check uses stable identifier `seo.hreflang`.
- The feature lives under `src/Checks/Hreflang/`.

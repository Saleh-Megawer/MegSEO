# Feature Specification: Open Graph Check

**Feature Branch**: `006-open-graph-check`  
**Created**: 2026-06-13  
**Status**: Draft  
**Input**: User description: "Create the next MegSEO feature: Open Graph Check. Analyze Open Graph metadata and provide actionable social SEO recommendations."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Validate Core Open Graph Tags (Priority: P1)

As a Laravel developer using MegSEO, I want the Open Graph Check to evaluate whether essential OG tags (title, description, image) are present and well-formed so that I can ensure my pages render correctly when shared on social platforms.

**Why this priority**: Missing or broken OG tags cause blank or generic social previews, directly harming engagement and click-through from social media.

**Independent Test**: Submit content contexts with and without og:title, og:description, and og:image values; verify correct issues for missing, empty, and valid values.

**Acceptance Scenarios**:

1. **Given** all essential OG tags (title, description, image) with valid values, **When** the Open Graph Check runs, **Then** it returns no issues.
2. **Given** missing og:title, **When** the check runs, **Then** it returns an issue describing the missing tag.
3. **Given** missing og:description, **When** the check runs, **Then** it returns an issue.
4. **Given** missing og:image, **When** the check runs, **Then** it returns an issue.
5. **Given** empty values for any core OG property, **When** the check runs, **Then** it returns an issue.

---

### User Story 2 - Validate OG Image Quality (Priority: P2)

As a developer or content creator, I want the Open Graph Check to validate og:image URLs and flag relative URLs or other quality concerns so that social platforms can reliably fetch and display images.

**Why this priority**: Invalid or relative image URLs cause broken social previews. Image validation is the second most critical OG concern after presence.

**Independent Test**: Submit og:image values with valid absolute URLs, relative URLs, and invalid URLs; verify correct findings.

**Acceptance Scenarios**:

1. **Given** a valid absolute og:image URL, **When** the check runs, **Then** it returns no image-related findings.
2. **Given** a relative og:image URL, **When** the check runs, **Then** it returns a warning recommending absolute URLs.
3. **Given** an invalid og:image URL (malformed, non-http scheme), **When** the check runs, **Then** it returns an issue.
4. **Given** multiple og:image tags, **When** the check runs, **Then** it returns a suggestion or metadata noting the multiple values.

---

### User Story 3 - Act as the Social Metadata Reference Pattern (Priority: P3)

As a third-party MegSEO feature author, I want the Open Graph Check to follow the same architectural patterns as previous checks so that I can reference it when designing social metadata or structured data checks.

**Why this priority**: MegSEO needs a reference implementation for social metadata checks. Open Graph Check is the first of this category.

**Independent Test**: Verify deterministic repeated runs, stable identifiers, score contributions with rationale, and metadata consistency.

**Acceptance Scenarios**:

1. **Given** identical OG tag inputs, **When** the check runs repeatedly, **Then** it produces identical findings, scores, and metadata.
2. **Given** OG tags with Unicode characters, **When** the check runs, **Then** the content is analyzed correctly.
3. **Given** dashboard consumers rely on stable identifiers, **When** the check emits findings, **Then** identifiers and metadata remain stable.

---

### Edge Cases

- What happens when all OG tags are missing?
- What happens when og:image is the only missing tag?
- How does the check handle empty strings vs whitespace-only values?
- What happens with multiple conflicting og:title values?
- How does the check handle og:image URLs with query parameters?
- What happens when OG data is supplied as a partial object (some keys missing)?
- How does the check handle non-standard OG properties?
- How does the check avoid nondeterministic outcomes?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The system MUST evaluate og:title presence and identify when it is missing.
- **FR-002**: The system MUST evaluate og:description presence and identify when it is missing.
- **FR-003**: The system MUST evaluate og:image presence and identify when it is missing.
- **FR-004**: The system MUST identify empty Open Graph values as unusable.
- **FR-005**: The system MUST validate og:image URLs as well-formed absolute URLs.
- **FR-006**: The system MUST detect relative og:image URLs and recommend absolute URLs.
- **FR-007**: The system MUST detect multiple conflicting OG values when supplied.
- **FR-008**: The system MUST provide issues for severe OG problems.
- **FR-009**: The system MUST provide warnings for moderate concerns.
- **FR-010**: The system MUST provide suggestions for improvements.
- **FR-011**: The system MUST provide score contributions with clear rationale.
- **FR-012**: The system MUST provide confidence values where appropriate.
- **FR-013**: The system MUST emit stable identifiers and metadata.
- **FR-014**: The system MUST handle missing OG data safely without crashing.
- **FR-015**: The system MUST keep all analysis deterministic.
- **FR-016**: The system MUST remain within Open Graph scope — no HTTP requests, image downloads, crawling, or external services.
- **FR-017**: The system MUST reuse the `CanonicalUrlValidator` for og:image URL validation.
- **FR-018**: The system MUST support Unicode and IDN characters in OG values and image URLs.
- **FR-019**: The system MUST follow the same architectural patterns as Title Check, Meta Description Check, and Canonical Check.

### Key Entities

- **Open Graph Input**: The OG tag values supplied for analysis — og:title, og:description, og:image, and any additional OG properties.
- **OG Tag Finding**: A structured output item representing an issue, warning, or suggestion about a specific OG property.
- **OG Image URL**: The og:image value validated using the existing URL validation infrastructure.
- **OG Score Contribution**: The structured score impact emitted by the check.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: In validation scenarios covering all missing, empty, valid, invalid image, and relative image OG inputs, the check returns the expected finding category in each case.
- **SC-002**: Repeated runs with identical OG input produce identical outputs in 100% of runs.
- **SC-003**: The feature follows the same architectural patterns as previous checks.
- **SC-004**: Developers can understand and extend the feature using public contracts and conventions.
- **SC-005**: Existing Core, Title Check, Meta Description Check, and Canonical Check behavior remain unchanged.

## Assumptions

- The MegSEO core engine and check integration foundation already exist.
- The `CanonicalUrlValidator` is reused for og:image URL validation.
- Open Graph data is supplied via the analysis context as structured data (associative array of OG properties).
- OG properties include at minimum og:title, og:description, and og:image.
- Additional non-standard OG properties are ignored by the check but preserved in metadata.
- No HTTP requests are made to verify image accessibility or dimensions.
- The check uses the stable identifier `seo.open_graph`.
- The feature lives under `src/Checks/OpenGraph/` following convention.

# Feature Specification: Twitter Card Check

**Feature Branch**: `007-twitter-card-check`  
**Created**: 2026-06-13  
**Status**: Draft  
**Input**: User description: "Create the next MegSEO feature: Twitter Card Check. Analyze Twitter Card metadata and provide actionable social SEO recommendations."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Validate Core Twitter Card Tags (Priority: P1)

As a Laravel developer using MegSEO, I want the Twitter Card Check to evaluate whether essential Twitter tags (card, title, description, image) are present and well-formed so that my pages render correctly when shared on Twitter/X.

**Why this priority**: Missing Twitter Card tags cause generic or broken link previews, directly harming engagement from Twitter/X.

**Independent Test**: Submit content with and without twitter:card, twitter:title, twitter:description, and twitter:image; verify correct findings.

**Acceptance Scenarios**:

1. **Given** all essential Twitter tags with valid values, **When** the Twitter Card Check runs, **Then** it returns no issues.
2. **Given** missing twitter:card, **When** the check runs, **Then** it returns an issue.
3. **Given** missing twitter:title, **When** the check runs, **Then** it returns an issue.
4. **Given** missing twitter:description, **When** the check runs, **Then** it returns an issue.
5. **Given** missing twitter:image, **When** the check runs, **Then** it returns an issue.

---

### User Story 2 - Validate Card Quality (Priority: P2)

As a developer, I want the Twitter Card Check to validate card types, image URLs, and detect conflicting values so that I can ensure my Twitter Cards are technically correct.

**Why this priority**: Invalid card types and broken images undermine Twitter Card effectiveness. Quality validation is the next layer after basic presence.

**Independent Test**: Submit card types (valid and invalid), image URLs (absolute, relative, invalid), and conflicting values; verify correct findings.

**Acceptance Scenarios**:

1. **Given** a valid card type (summary, summary_large_image, app, player), **When** the check runs, **Then** it returns no card-type findings.
2. **Given** an invalid card type, **When** the check runs, **Then** it returns a warning.
3. **Given** a relative twitter:image URL, **When** the check runs, **Then** it returns a warning.
4. **Given** conflicting values for the same Twitter property, **When** the check runs, **Then** it returns a suggestion.
5. **Given** duplicate identical values, **When** the check runs, **Then** no conflict finding is emitted.

---

### User Story 3 - Act as a Consistent Social Metadata Pattern (Priority: P3)

As a third-party MegSEO feature author, I want the Twitter Card Check to follow the same architectural patterns as Open Graph Check so that social metadata checks are consistent and predictable.

**Why this priority**: MegSEO needs consistent patterns across social metadata checks. Twitter Card Check reinforces the Open Graph pattern.

**Independent Test**: Verify deterministic repeated runs, stable identifiers, score contributions with rationale, and metadata consistency.

**Acceptance Scenarios**:

1. **Given** identical Twitter Card inputs, **When** the check runs repeatedly, **Then** it produces identical findings, scores, and metadata.
2. **Given** Twitter tags with Arabic/Unicode values, **When** the check runs, **Then** content is analyzed correctly.
3. **Given** dashboard consumers rely on stable identifiers, **When** the check emits findings, **Then** identifiers and metadata remain stable.

---

### Edge Cases

- What happens when all Twitter Card tags are missing?
- How does the check handle empty strings vs whitespace-only values?
- What happens with multiple conflicting twitter:card values?
- How does the check handle twitter:image URLs with IDN/Unicode paths?
- What happens when Twitter data is supplied as a partial object?
- How does the check handle non-standard Twitter properties?
- How does the check avoid nondeterministic outcomes?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST evaluate twitter:card presence.
- **FR-002**: System MUST evaluate twitter:title presence.
- **FR-003**: System MUST evaluate twitter:description presence.
- **FR-004**: System MUST evaluate twitter:image presence.
- **FR-005**: System MUST identify empty values as unusable. Empty suppresses missing.
- **FR-006**: System MUST validate twitter:card type against supported values (summary, summary_large_image, app, player).
- **FR-007**: System MUST validate twitter:image URLs using CanonicalUrlValidator.
- **FR-008**: System MUST detect relative twitter:image URLs and recommend absolute URLs.
- **FR-009**: System MUST detect conflicting values (different values only; duplicates ignored).
- **FR-010**: System MUST provide issues, warnings, and suggestions as appropriate.
- **FR-011**: System MUST provide score contributions with clear rationale.
- **FR-012**: System MUST emit stable identifiers and metadata.
- **FR-013**: System MUST handle missing data safely.
- **FR-014**: System MUST keep all analysis deterministic.
- **FR-015**: No HTTP requests, image downloads, crawling, or external services.
- **FR-016**: System MUST reuse CanonicalUrlValidator for image URL validation.
- **FR-017**: System MUST support Unicode in Twitter values and image URLs.
- **FR-018**: System MUST follow the same architectural patterns as Open Graph Check.

### Key Entities

- **Twitter Card Input**: Structured array of twitter: prefixed properties.
- **Twitter Card Finding**: Issue, warning, or suggestion about a Twitter Card property.
- **Twitter Image URL**: Validated using CanonicalUrlValidator.
- **Twitter Score Contribution**: Score impact with rationale.

## Success Criteria *(mandatory)*

- **SC-001**: In validation scenarios covering all Twitter Card states, the check returns the expected finding category.
- **SC-002**: Repeated runs produce identical outputs in 100% of runs.
- **SC-003**: Follows the same architectural patterns as Open Graph Check.
- **SC-004**: Existing Core and all feature check behavior remain unchanged.
- **SC-005**: Cross-feature reuse of CanonicalUrlValidator works correctly.

## Assumptions

- MegSEO core and previous checks exist and are stable.
- `CanonicalUrlValidator` is reused for twitter:image URLs.
- Data arrives as a structured array of `twitter:` prefixed properties.
- Empty values suppress missing findings (pattern from Open Graph Check).
- Duplicate identical values are not conflicts (pattern from Open Graph Check).
- The check uses stable identifier `seo.twitter_card`.
- The feature lives under `src/Checks/TwitterCard/`.

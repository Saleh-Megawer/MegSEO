# Feature Specification: Canonical URL IDN/Unicode Validation Fix

**Feature Branch**: `005-canonical-idn-fix`  
**Created**: 2026-06-13  
**Status**: Draft  
**Input**: User description: "Improve CanonicalUrlValidator to correctly validate internationalized URLs without changing existing public APIs."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Validate International Canonical URLs (Priority: P1)

As a developer using MegSEO on an Arabic or international website, I want the Canonical Check to correctly validate my canonical URLs even when they contain non-ASCII path segments or IDN domains, so that valid URLs are not incorrectly flagged as invalid or relative.

**Why this priority**: False positives on valid international URLs undermine trust in the tool and affect a core use case (Arabic-first support).

**Independent Test**: Submit canonical URLs with Arabic path segments, IDN domains, and mixed Unicode content; verify they pass validation and do not produce false relative warnings.

**Acceptance Scenarios**:

1. **Given** a canonical URL with Arabic path `https://example.com/دليل`, **When** URL validation runs, **Then** the URL is accepted as valid and not flagged as relative.
2. **Given** an IDN domain `https://موقعي.مصر/صفحة`, **When** URL validation runs, **Then** the URL is accepted as valid.
3. **Given** invalid URLs like `javascript:void(0)`, `ftp://example.com`, `mailto:test@example.com`, **When** URL validation runs, **Then** they are rejected as invalid.
4. **Given** malformed URLs like `not-a-url`, `https://`, **When** URL validation runs, **Then** they are rejected as invalid.
5. **Given** identical Arabic URL inputs, **When** validation runs repeatedly, **Then** results are deterministic.

---

### User Story 2 - Preserve Existing Behavior (Priority: P2)

As a developer who has integrated MegSEO into their workflow, I want all existing Canonical Check behavior to remain unchanged so that nothing breaks in my current setup.

**Why this priority**: The fix must not introduce regressions. Existing validations (invalid schemes, empty hosts, missing data) must continue to work identically.

**Independent Test**: Run the full existing test suite and verify zero regressions.

**Acceptance Scenarios**:

1. **Given** the existing test suite (393 tests), **When** the fix is applied, **Then** all existing tests continue to pass.
2. **Given** the full-stack dogfooding example, **When** the fix runs, **Then** the Arabic Website scenario no longer shows a false relative canonical warning.

---

### Edge Cases

- What happens with punycode IDN domains like `https://xn--mgbh0fb.xn--kgbechtv/`?
- How does the validator handle mixed ASCII and Unicode path segments?
- What happens with percent-encoded Unicode characters in URLs?
- How does the validator handle URLs with query strings containing Unicode?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: `CanonicalUrlValidator::isValid()` MUST use `parse_url` as the primary validation mechanism instead of relying solely on `FILTER_VALIDATE_URL`.
- **FR-002**: Valid URLs MUST have a scheme that is either `http` or `https`.
- **FR-003**: Valid URLs MUST have a non-empty host component.
- **FR-004**: URLs with non-http/https schemes (e.g., `ftp://`, `javascript:`, `mailto:`) MUST be rejected.
- **FR-005**: `CanonicalUrlValidator::isRelative()` MUST use `parse_url` to detect relative URLs by checking for the absence of a scheme component.
- **FR-006**: IDN domain names and Unicode path segments MUST be accepted as valid.
- **FR-007**: All existing rejection cases (invalid schemes, empty hosts, malformed URLs) MUST continue to be rejected.
- **FR-008**: Validation behavior MUST remain deterministic for identical inputs.

### Key Entities

- **CanonicalUrlValidator**: The URL validation helper class in `src/Checks/Canonical/Support/`. Methods `isValid()` and `isRelative()` are the only affected surface.
- **Canonical URL**: The input URL string to validate.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: The Arabic Website scenario in the full-stack dogfooding example no longer produces a false canonical warning.
- **SC-002**: All 393 existing tests pass without modification.
- **SC-003**: New IDN and Unicode validation tests pass.
- **SC-004**: Zero regressions across Core, Title Check, Meta Description Check, and Canonical Check.

## Assumptions

- `parse_url` in PHP 8.2 adequately parses IDN and Unicode URLs when the URL is well-formed.
- No HTTP requests are made during validation — validation is purely structural.
- The fix is limited to `CanonicalUrlValidator` only; no other files are modified.
- `filter_var` may still be used as a supplementary check but must not be the sole validation mechanism.

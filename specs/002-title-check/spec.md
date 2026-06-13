# Feature Specification: Title Check

**Feature Branch**: `002-title-check`  
**Created**: 2026-06-13  
**Status**: Draft  
**Input**: User description: "Create the first real MegSEO feature: Title Check.

The goal is to analyze page titles and provide actionable recommendations rather than vanity scoring.

Context:

MegSEO's platform foundation is complete and validated.

The Title Check must demonstrate how future SEO checks should be designed and integrated.

Objectives:

Analyze title quality and return meaningful findings.

Requirements:

The check must evaluate:

Title existence.
Empty titles.
Excessively short titles.
Excessively long titles.
Recommended title length ranges.
Duplicate title detection support.
Basic keyword presence support when a focus keyword is supplied.
Title normalization before analysis.
Whitespace-only titles.
Unicode and Arabic title support as first-class citizens.
Titles containing only punctuation or separators.
Safe handling of missing title data.

The check should provide:

Issues for severe problems.
Warnings for moderate concerns.
Suggestions for improvements.
Score contributions with clear rationale.
Confidence values where appropriate.
Stable identifiers and metadata suitable for future dashboards.

Non-goals:

No AI-generated title recommendations.
No external APIs.
No search engine scraping.
No click-through-rate prediction.
No competitor analysis.
No keyword stuffing detection outside title scope.

Acceptance scenarios should include:

Valid title passes without findings.
Missing title produces an issue.
Empty title produces an issue.
Short title produces a warning.
Long title produces a warning.
Focus keyword absent produces a suggestion.
Arabic titles are analyzed correctly.
Titles containing only separators fail appropriately.
Normalization produces deterministic behavior.

Success criteria:

The feature serves as the reference implementation for future checks.
Results remain deterministic.
Public contracts remain stable.
The implementation is easy for third-party developers to understand and extend.
The developer experience remains consistent with the existing MegSEO platform."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Analyze a Single Title Safely (Priority: P1)

As a Laravel developer using MegSEO, I want the Title Check to evaluate whether a page title is present, usable, and reasonably sized so that I can quickly understand severe title problems and fix them before publishing content.

**Why this priority**: This is the core value of the feature. Without reliable title presence and quality checks, the feature does not deliver meaningful SEO intelligence.

**Independent Test**: Can be fully tested by submitting content contexts with valid, missing, empty, whitespace-only, separator-only, short, and long titles and verifying that the check returns the correct issues, warnings, suggestions, and score contribution rationale.

**Acceptance Scenarios**:

1. **Given** a valid title within the recommended range, **When** the Title Check runs, **Then** it returns no severe findings for title presence or length.
2. **Given** missing title data, **When** the Title Check runs, **Then** it returns an issue describing the missing title and handles the input safely.
3. **Given** an empty, whitespace-only, or separator-only title, **When** the Title Check runs, **Then** it returns an issue indicating that the title is not usable.
4. **Given** a title that is shorter than the recommended range, **When** the Title Check runs, **Then** it returns a warning with an actionable explanation.
5. **Given** a title that is longer than the recommended range, **When** the Title Check runs, **Then** it returns a warning with an actionable explanation.

---

### User Story 2 - Provide Search-Relevant Guidance (Priority: P2)

As a developer or site maintainer, I want the Title Check to provide meaningful guidance about focus keyword presence and duplicate-title support so that I can improve title usefulness without relying on shallow scores alone.

**Why this priority**: Once basic title validity works, the next layer of value is practical improvement guidance that helps content teams strengthen search relevance.

**Independent Test**: Can be fully tested by running the Title Check with and without a supplied focus keyword, and with duplicate-title support data available, then verifying that the findings and metadata remain consistent and actionable.

**Acceptance Scenarios**:

1. **Given** a focus keyword is supplied and is not present in the normalized title, **When** the Title Check runs, **Then** it returns a suggestion explaining the missed opportunity.
2. **Given** a focus keyword is supplied and is present in the normalized title, **When** the Title Check runs, **Then** it does not return an unnecessary keyword-absence suggestion.
3. **Given** duplicate-title support data is available and the normalized title matches another page title, **When** the Title Check runs, **Then** it returns a finding or metadata signal supporting duplicate-title detection workflows.

---

### User Story 3 - Act as the Reference Check Pattern (Priority: P3)

As a third-party MegSEO feature author, I want the Title Check to model stable identifiers, score contribution rationale, confidence handling, metadata usage, and multilingual-safe normalization so that I can use it as the reference design for future checks.

**Why this priority**: MegSEO needs one real feature that demonstrates how future checks should be structured, integrated, and consumed across the platform.

**Independent Test**: Can be fully tested by verifying that repeated runs with identical inputs produce identical outputs, that public result contracts remain stable, and that Arabic and Unicode titles are processed consistently without breaking the general check contract.

**Acceptance Scenarios**:

1. **Given** identical title input, focus keyword input, and duplicate-title support data, **When** the Title Check runs repeatedly, **Then** it produces identical findings, metadata, and score contribution rationale.
2. **Given** an Arabic or other Unicode title, **When** the Title Check runs, **Then** the title is normalized and analyzed correctly without degrading multilingual content handling.
3. **Given** dashboard or reporting consumers rely on stable check identifiers and metadata, **When** the Title Check emits findings, **Then** the identifiers and metadata remain stable and suitable for future platform use.

---

### Edge Cases

- What happens when title data is completely missing from the analysis context?
- How does the check behave when the title is an empty string, whitespace-only, or composed only of separators or punctuation?
- What happens when normalization changes spacing, punctuation, or Unicode representation but should not change the intended title meaning?
- How does the check behave when a focus keyword is absent, empty, whitespace-only, or normalized differently from the title?
- What happens when duplicate-title support data is unavailable even though the check supports duplicate-title workflows?
- How does the check behave for Arabic titles, mixed Arabic-English titles, and other Unicode content?
- What happens when the title contains only symbols that appear meaningful visually but should fail as usable title text?
- How does the check avoid nondeterministic outcomes when repeated with the same title input and support data?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The system MUST analyze title existence and identify when title data is missing.
- **FR-002**: The system MUST identify empty titles and whitespace-only titles as unusable.
- **FR-003**: The system MUST identify titles containing only punctuation, separators, or equivalent non-meaningful characters as unusable.
- **FR-004**: The system MUST evaluate whether a title is excessively short.
- **FR-005**: The system MUST evaluate whether a title is excessively long.
- **FR-006**: The system MUST evaluate titles against recommended title length ranges.
- **FR-007**: The system MUST normalize titles before analysis so repeated evaluation is deterministic.
- **FR-008**: The system MUST support duplicate title detection workflows when duplicate-title support data is available.
- **FR-009**: The system MUST support basic focus keyword presence analysis when a focus keyword is supplied.
- **FR-010**: The system MUST provide issues for severe title problems.
- **FR-011**: The system MUST provide warnings for moderate title concerns.
- **FR-012**: The system MUST provide suggestions for title improvements.
- **FR-013**: The system MUST provide score contributions with clear rationale tied to the title findings.
- **FR-014**: The system MUST provide confidence values where confidence signaling is appropriate.
- **FR-015**: The system MUST emit stable identifiers and metadata suitable for future dashboards and reporting consumers.
- **FR-016**: The system MUST treat Unicode and Arabic title handling as first-class behavior rather than optional add-on support.
- **FR-017**: The system MUST handle missing title data safely without crashing or producing misleading conclusions.
- **FR-018**: The system MUST keep all analysis deterministic for identical title input, focus keyword input, normalization behavior, and duplicate-title support data.
- **FR-019**: The system MUST remain within title scope and MUST NOT include AI-generated recommendations, external APIs, search engine scraping, click-through-rate prediction, competitor analysis, or keyword stuffing detection outside title scope.
- **FR-020**: The system MUST serve as the reference implementation pattern for future MegSEO checks in how it structures findings, rationale, confidence values, identifiers, and metadata.

### Key Entities *(include if feature involves data)*

- **Title Input**: The raw title value supplied for analysis, including cases where the value is missing, empty, whitespace-only, or separator-only.
- **Normalized Title**: The title value after deterministic normalization used for length analysis, keyword presence checks, and duplicate-title support.
- **Focus Keyword Input**: An optional keyword or phrase supplied to support basic title keyword presence analysis.
- **Duplicate Title Support Data**: Optional supporting data used to determine whether the normalized title duplicates another known page title.
- **Title Check Finding**: A structured output item representing an issue, warning, or suggestion produced by the Title Check, including rationale, confidence where appropriate, and stable metadata.
- **Title Score Contribution**: The structured score impact emitted by the Title Check together with an explanation of why the title quality affected the score.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: In validation scenarios covering valid, missing, empty, whitespace-only, separator-only, short, long, Arabic, and keyword-aware title inputs, the Title Check returns the expected finding category in each case.
- **SC-002**: Repeated Title Check runs with identical title input, focus keyword input, normalization behavior, and duplicate-title support data produce identical outputs in 100% of validation runs.
- **SC-003**: The feature can be used as the reference implementation for future MegSEO checks by demonstrating stable identifiers, score contribution rationale, confidence signaling, and structured metadata in one consistent contract.
- **SC-004**: Developers can understand and extend the feature using only the public contracts, documentation, and platform conventions without needing hidden Title Check knowledge.
- **SC-005**: The feature preserves stable public contracts so downstream consumers can continue using findings, score contributions, and metadata without unexpected structural changes.
- **SC-006**: The developer experience for running and consuming the Title Check remains consistent with the established MegSEO platform foundation.

## Assumptions

- The MegSEO core engine and check integration foundation already exist and are available for feature-level checks.
- Recommended title length ranges will be evaluated according to project-defined rules rather than ad hoc per-request behavior.
- Duplicate-title support depends on optional supporting data and does not require external services.
- Basic focus keyword presence analysis is limited to checking whether the supplied keyword appears in the normalized title.
- Confidence signaling is used only where it adds clarity and does not replace direct actionable findings.

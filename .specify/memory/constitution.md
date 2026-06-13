# MegSEO Constitution

## Core Principles

### I. Architecture Before Features
MegSEO is an SEO Intelligence Engine for Laravel, not a simple SEO scoring plugin. Features must be designed to help developers understand why content may underperform and what to do next.

No feature may be implemented before its specification is approved. Every feature follows this lifecycle: Idea -> Specification -> Review -> Tasks -> Implementation -> Testing -> Documentation -> Release. Direct coding without specifications is not allowed.

AI agents are implementers, not architects. They must not expand scope, invent architectural direction, or bypass approved specifications without explicit approval.

### II. Feature-Driven Development
MegSEO must be organized around features instead of generic technical layers. Feature code should live in feature-centered structures such as `TraditionalSEO/Title/TitleCheck.php`, not broad buckets such as `Checks/TitleCheck.php`.

Each feature should encapsulate its own logic, tests, documentation, configuration, and supporting classes whenever practical. Features should describe user-facing SEO behavior in terms of insights and recommendations, not just pass/fail checks.

### III. Generic Core, Domain-Specific Features
The Core Engine is responsible only for executing checks, managing pipelines, aggregating results, and providing extension points. The Core Engine must remain generic and framework-agnostic. It must never contain SEO-specific business rules.

All SEO behavior belongs inside feature modules. Core abstractions may support execution and extensibility, but domain rules, heuristics, thresholds, messaging, and recommendations must live in feature-level implementations.

### IV. Open for Extension
MegSEO must be easy to extend without modifying existing core behavior. Prefer composition over inheritance, favor interfaces and contracts, and avoid rigid implementations that make new checks or features difficult to add.

Avoid hardcoded SEO thresholds whenever practical. Rules, scoring criteria, and tunable heuristics should be configurable so teams can adapt MegSEO to different sites, content strategies, and languages without forking core behavior.

### V. Quality, Documentation, and Stability Are Mandatory
Every feature must include tests and documentation before it is considered complete. Minimum testing requirements are unit tests and edge case tests, with Laravel integration tests required for framework integrations.

Public APIs and output contracts must be treated as stable. Breaking changes must be explicitly documented, justified, and versioned according to semantic versioning, with careful consideration for consumers that rely on result shapes, recommendation payloads, and extension contracts.

## Product Constraints

MegSEO prioritizes actionable insights over vanity scoring. Scores may exist as supporting signals, but they are always secondary to useful guidance.

Every detected issue must explain:
- what the issue is,
- why it matters,
- and how to improve it.

Recommendations should be concrete, educational, and actionable. MegSEO should help developers improve titles, metadata, internal links, topic coverage, structure, and search intent alignment rather than merely labeling content as good or bad.

Recommendations should educate users, not merely instruct them. MegSEO should help developers understand SEO concepts and reasoning so they can make better decisions beyond the immediate issue.

False positives are worse than missing suggestions. Recommendations must communicate confidence appropriately and must not present uncertain conclusions as facts. When heuristics are probabilistic or context-sensitive, the output should reflect that uncertainty clearly.

Human judgment always overrides automated recommendations. MegSEO provides guidance, not absolute truth, especially in areas where SEO outcomes are context-dependent.

Modern SEO takes priority over legacy checklists. Traditional SEO remains supported, but future development should emphasize semantic SEO, search intent, topic coverage, E-E-A-T signals, internal linking intelligence, structured data, and competitor intelligence. MegSEO should avoid becoming a shallow keyword counter.

Arabic is a first-class citizen and a strategic competitive advantage. Language-sensitive functionality must be designed for Arabic, English, and future languages from the start. This includes tokenization, word counting, readability analysis, transition words, density calculations, and recommendation wording that respects linguistic differences rather than treating Arabic as an afterthought.

MegSEO must deliver a Laravel-first developer experience. Public integration surfaces should feel native to Laravel, including fluent APIs, service providers, facades where appropriate, artisan commands, testability, and flexible configuration. Laravel convenience must not compromise the framework-agnostic nature of the core engine.

Progressive enhancement is preferred. Optional integrations and external services may enhance functionality, but they must never be required for MegSEO to be useful in core scenarios. Core functionality must remain accessible without paid or mandatory external APIs.

## Development Workflow

MegSEO prioritizes maintainability over shortcuts, extensibility over rigidity, clarity over cleverness, developer experience over unnecessary abstraction, and long-term sustainability over rapid feature accumulation.

Release philosophy favors small, stable, incremental improvements over large unfinished feature batches. It is better to ship focused, well-tested intelligence capabilities than broad but incomplete systems that erode trust.

Given identical inputs and configuration, MegSEO should produce consistent outputs. Non-deterministic behavior must be explicitly documented and justified.

AI is an implementation assistant, not an architect. Architectural decisions must come from approved specifications and project principles. AI-generated code must not bypass specifications, testing, documentation, review expectations, or expand specification scope without explicit approval.

Definition of Done is strict. A feature is incomplete until all of the following exist:
- approved specification and acceptance criteria,
- implementation,
- unit tests and relevant edge case coverage,
- Laravel integration tests where applicable,
- documentation,
- stable output behavior aligned with public contracts,
- and relevant extension points or configuration hooks where the feature reasonably requires them.

Contributors should optimize for long-term maintainability and make each feature understandable, testable, configurable, and easy to extend.

## Governance

This constitution supersedes informal implementation habits and feature shortcuts. All specifications, tasks, reviews, and implementations must be evaluated against these principles.

Changes to this constitution must be explicit, documented, and justified. Any amendment that affects public APIs, output contracts, feature workflow, language support expectations, Laravel integration expectations, or extension model must include a migration and compatibility rationale.

All contributors, whether human or AI-assisted, are responsible for preserving the generic core, feature-driven architecture, Laravel-first developer experience, configurable SEO behavior, recommendation quality, test coverage, documentation quality, and multilingual design expectations defined here.

**Version**: 1.1.0 | **Ratified**: 2026-06-13 | **Last Amended**: 2026-06-13

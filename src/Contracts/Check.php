<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;

/**
 * Extension point contract for all MegSEO analysis checks.
 *
 * Checks operate independently, accept an immutable AnalysisContext,
 * and return a structured CheckOutcome for aggregation.
 *
 * ## Stable Identifier
 *
 * The identifier returned by {@see ref()} MUST be stable once published.
 * Changing a check's identifier is a breaking change that affects
 * reporting, filtering, debugging, and dashboard consumers.
 *
 * ## Determinism
 *
 * Calling {@see analyze()} with identical context inputs MUST produce
 * identical outputs, unless non-deterministic behavior is explicitly
 * documented and justified.
 */
interface Check
{
    /**
     * Returns the stable reference for this check.
     *
     * The identifier must remain unchanged across releases once public.
     */
    public function ref(): CheckReference;

    /**
     * Analyzes the given context and returns structured findings.
     */
    public function analyze(AnalysisContext $context): CheckOutcome;
}

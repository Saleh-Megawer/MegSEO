<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisResult;

interface AnalyzesContexts
{
    public function analyze(AnalysisContext $context): AnalysisResult;
}

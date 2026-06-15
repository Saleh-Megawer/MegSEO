<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph\Rules;

use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingOgDescription
{
    public function evaluate(OpenGraphCheckInput $input, bool $isEmptyDetected): ?AnalysisIssue
    {
        if ($isEmptyDetected) {
            return null;
        }

        if (! $input->has('og:description')) {
            return new AnalysisIssue(
                message: 'og:description is missing',
                details: 'The og:description tag provides the description text shown in social media previews.',
                sourceCheckId: 'seo.open_graph',
            );
        }

        return null;
    }
}

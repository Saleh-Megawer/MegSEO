<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph\Rules;

use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingOgTitle
{
    public function evaluate(OpenGraphCheckInput $input, bool $isEmptyDetected): ?AnalysisIssue
    {
        if ($isEmptyDetected) {
            return null;
        }

        if (! $input->has('og:title')) {
            return new AnalysisIssue(
                message: 'og:title is missing',
                details: 'The og:title tag is required for Open Graph. It defines the title shown in social media previews.',
                sourceCheckId: 'seo.open_graph',
            );
        }

        return null;
    }
}

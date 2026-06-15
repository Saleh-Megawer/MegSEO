<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph\Rules;

use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectEmptyOgValues
{
    /** @return array<int, AnalysisIssue> */
    public function evaluate(OpenGraphCheckInput $input): array
    {
        $issues = [];
        $keys = ['og:title', 'og:description', 'og:image'];

        foreach ($keys as $key) {
            if ($input->isEmpty($key)) {
                $label = str_replace('og:', '', $key);
                $issues[] = new AnalysisIssue(
                    message: "og:{$label} is empty",
                    details: "The {$key} tag is present but its value is empty. Provide a meaningful value for proper social media previews.",
                    sourceCheckId: 'seo.open_graph',
                );
            }
        }

        return $issues;
    }
}

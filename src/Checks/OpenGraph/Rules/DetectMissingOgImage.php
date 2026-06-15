<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph\Rules;

use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;
use MegSEO\DTO\AnalysisIssue;

final readonly class DetectMissingOgImage
{
    public function evaluate(OpenGraphCheckInput $input, bool $isEmptyDetected): ?AnalysisIssue
    {
        if ($isEmptyDetected) {
            return null;
        }

        if (! $input->has('og:image')) {
            return new AnalysisIssue(
                message: 'og:image is missing',
                details: 'The og:image tag is essential for social media previews. Without it, platforms may show no image or a random one.',
                sourceCheckId: 'seo.open_graph',
            );
        }

        return null;
    }
}

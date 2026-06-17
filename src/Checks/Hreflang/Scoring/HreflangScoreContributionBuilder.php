<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\Scoring;

use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\ScoreSummary;

final readonly class HreflangScoreContributionBuilder
{
    private const MAX = 100.0;
    private const PEN_MISSING = 40.0;
    private const PEN_EMPTY = 25.0;
    private const PEN_INVALID_LANG = 15.0;
    private const PEN_INVALID_URL = 15.0;
    private const PEN_RELATIVE = 10.0;
    private const PEN_NO_XDEFAULT = 5.0;
    private const PEN_SELF_REF = 5.0;
    private const PEN_DUP_LANG = 5.0;
    private const PEN_SAME_HREF = 10.0;

    public function build(array $issues, array $warnings, array $suggestions, array $metadata = []): ScoreSummary
    {
        $d = 0.0; $c = []; $r = [];

        foreach ($issues as $i) { $p = $this->penaltyIssue($i); $d += $p; $c[] = ['value'=>-$p,'sourceCheckId'=>$i->sourceCheckId]; $r[] = ['finding'=>$i->message,'severity'=>'issue','deduction'=>$p]; }
        foreach ($warnings as $w) { $p = $this->penaltyWarning($w); $d += $p; $c[] = ['value'=>-$p,'sourceCheckId'=>$w->sourceCheckId]; $r[] = ['finding'=>$w->message,'severity'=>'warning','deduction'=>$p]; }
        foreach ($suggestions as $s) { $p = $this->penaltySuggestion($s); $d += $p; $c[] = ['value'=>-$p,'sourceCheckId'=>$s->sourceCheckId]; $r[] = ['finding'=>$s->message,'severity'=>'suggestion','deduction'=>$p]; }

        return new ScoreSummary(value: round(max(0.0, self::MAX - $d), 1), contributors: $c, metadata: ['max_score'=>self::MAX,'total_deductions'=>round($d,1),'rationale'=>$r]);
    }

    private function penaltyIssue(AnalysisIssue $i): float {
        return match(true) {
            str_contains($i->message,'missing') => self::PEN_MISSING,
            str_contains($i->message,'empty') => self::PEN_EMPTY,
            default => 10.0,
        };
    }

    private function penaltyWarning(AnalysisWarning $w): float {
        return match(true) {
            str_contains($w->message,'Same href') => self::PEN_SAME_HREF,
            str_contains($w->message,'language') => self::PEN_INVALID_LANG,
            str_contains($w->message,'invalid') => self::PEN_INVALID_URL,
            str_contains($w->message,'relative') => self::PEN_RELATIVE,
            default => 5.0,
        };
    }

    private function penaltySuggestion(AnalysisSuggestion $s): float {
        return match(true) {
            str_contains($s->message,'x-default') => self::PEN_NO_XDEFAULT,
            str_contains($s->message,'self-reference') => self::PEN_SELF_REF,
            str_contains($s->message,'Duplicate') => self::PEN_DUP_LANG,
            default => 3.0,
        };
    }
}

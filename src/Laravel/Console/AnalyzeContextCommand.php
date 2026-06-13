<?php

declare(strict_types=1);

namespace MegSEO\Laravel\Console;

use Illuminate\Console\Command;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

final class AnalyzeContextCommand extends Command
{
    protected $signature = 'megseo:analyze
                            {subject : The analysis subject (string or JSON)}';

    protected $description = 'Run a MegSEO analysis session against a context';

    public function handle(Engine $engine): int
    {
        $subject = $this->argument('subject');

        $context = new AnalysisContext(subject: $subject);

        $this->info('Running MegSEO analysis...');
        $this->newLine();

        $result = $engine->analyze($context);

        $this->info('Analysis Result');
        $this->line(str_repeat('-', 40));

        $score = $result->score();
        $this->info(sprintf(
            'Score: %s',
            $score->value !== null ? (string) $score->value : 'N/A',
        ));

        $this->info(sprintf('Issues: %d', count($result->issues())));
        foreach ($result->issues() as $issue) {
            $this->warn("  - {$issue->message}");
        }

        $this->info(sprintf('Warnings: %d', count($result->warnings())));
        foreach ($result->warnings() as $warning) {
            $this->line("  - {$warning->message}");
        }

        $this->info(sprintf('Suggestions: %d', count($result->suggestions())));
        foreach ($result->suggestions() as $suggestion) {
            $this->line("  - {$suggestion->message}");
        }

        $this->info(sprintf('Failures: %d', count($result->failures)));
        foreach ($result->failures as $failure) {
            $this->error("  - {$failure['check']->id}: {$failure['error']}");
        }

        return self::SUCCESS;
    }
}

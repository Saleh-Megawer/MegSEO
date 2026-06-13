<?php

declare(strict_types=1);

namespace MegSEO\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use MegSEO\Contracts\ExecutionPolicy;
use MegSEO\Core\Engine;
use MegSEO\Policy\FailFastExecutionPolicy;
use MegSEO\Policy\IsolateFailuresExecutionPolicy;
use MegSEO\Policy\StandardExecutionPolicies;

final class MegSEOServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Configuration/megseo.php',
            'megseo',
        );

        $this->app->singleton('megseo.engine', function ($app): Engine {
            $policy = $this->resolveExecutionPolicy();

            return Engine::make($policy);
        });

        $this->app->alias('megseo.engine', Engine::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Configuration/megseo.php' => config_path('megseo.php'),
            ], 'megseo-config');

            $this->commands([
                \MegSEO\Laravel\Console\AnalyzeContextCommand::class,
            ]);
        }

        $this->registerConfiguredChecks();
    }

    private function resolveExecutionPolicy(): ?ExecutionPolicy
    {
        $policyConfig = $this->app['config']->get('megseo.execution_policy', 'isolate_failures');

        return match ($policyConfig) {
            'fail_fast' => StandardExecutionPolicies::failFast(),
            'isolate_failures' => StandardExecutionPolicies::isolateFailures(),
            default => null,
        };
    }

    private function registerConfiguredChecks(): void
    {
        /** @var Engine $engine */
        $engine = $this->app->make('megseo.engine');
        $checkClasses = $this->app['config']->get('megseo.checks', []);

        if (count($checkClasses) > 0) {
            \MegSEO\Laravel\Support\LaravelCheckRegistration::registerFromConfig(
                $engine,
                $checkClasses,
            );
        }
    }
}

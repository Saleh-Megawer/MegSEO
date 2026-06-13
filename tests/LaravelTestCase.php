<?php

declare(strict_types=1);

namespace MegSEO\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use MegSEO\Laravel\Providers\MegSEOServiceProvider;

abstract class LaravelTestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [MegSEOServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('megseo.execution_policy', 'isolate_failures');
        $app['config']->set('megseo.checks', []);
    }
}

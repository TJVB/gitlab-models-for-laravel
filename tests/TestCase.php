<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use TJVB\GitlabModelsForLaravel\Providers\GitlabModelsProvider;

abstract class TestCase extends OrchestraTestCase
{
    public const EXAMPLE_PAYLOADS = __DIR__ . '/example_payloads/';

    protected function getPackageProviders($app): array
    {
        return [
            GitlabModelsProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        # Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}

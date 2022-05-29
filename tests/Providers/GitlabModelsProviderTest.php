<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Providers;

use TJVB\GitlabModelsForLaravel\Providers\GitlabModelsProvider;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class GitlabModelsProviderTest extends TestCase
{
    /**
     * @test
     */
    public function weProviderEveryItemFromTheProvidesArray(): void
    {
        // setup / mock
        $provider = new GitlabModelsProvider($this->app);

        // run
        $provides = $provider->provides();
        foreach ($provides as $contract) {
            $resolved = $this->app->make($contract);
            $this->assertInstanceOf($contract, $resolved);
        }
    }
}

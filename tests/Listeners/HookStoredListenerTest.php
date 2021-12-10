<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Listeners;

use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Listeners\HookStoredListener;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class HookStoredListenerTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // setup / mock

        // run
        $listener = new HookStoredListener();
// verify/assert
        $this->assertInstanceOf(GitLabHookStoredListener::class, $listener);
    }
}

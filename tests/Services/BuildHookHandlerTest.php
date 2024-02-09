<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Services\BuildHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeBuildUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use function Safe\file_get_contents;
use function Safe\json_decode;

final class BuildHookHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // setup / mock

        // run
        $handler = $this->app->make(BuildHookHandler::class);

        // verify/assert
        $this->assertInstanceOf(BuildHookHandler::class, $handler);
        $this->assertInstanceOf(BuildHookHandlerContract::class, $handler);
    }

    /**
     * @test
     */
    public function weStoreTheBuildData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'job.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'build';
        $buildUpdater = new FakeBuildUpdateService();

        // run
        $handler = new BuildHookHandler($buildUpdater);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($buildUpdater->receivedData);
    }
}

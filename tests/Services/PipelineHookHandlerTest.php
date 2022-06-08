<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Services\PipelineHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeBuildUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeMergeRequestUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakePipelineUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class PipelineHookHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $handler = $this->app->make(PipelineHookHandler::class);

        // verify/assert
        $this->assertInstanceOf(PipelineHookHandler::class, $handler);
        $this->assertInstanceOf(PipelineHookHandlerContract::class, $handler);
    }

    /**
     * @test
     */
    public function weStoreThePipelineData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'pipeline.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'pipeline';
        $buildUpdateService = new FakeBuildUpdateService();
        $mergeRequestUpdateService = new FakeMergeRequestUpdateService();
        $pipelineUpdateService = new FakePipelineUpdateService();
        $projectUpdateService = new FakeProjectUpdateService();

        // run
        $handler = new PipelineHookHandler(
            $buildUpdateService,
            $mergeRequestUpdateService,
            $pipelineUpdateService,
            $projectUpdateService
        );
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($buildUpdateService->receivedData);
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($pipelineUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
    }
}

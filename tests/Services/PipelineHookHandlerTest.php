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

    /**
     * @test
     */
    public function weDontStoreInvalidPipelineData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'pipeline.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'pipeline';
        $hookModel->body['object_attributes'] = 'invalid pipeline';
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
        $this->assertEmpty($pipelineUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDoNotStoreInvalidProjectData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'pipeline.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'pipeline';
        $hookModel->body['project'] = 'invalid projectdata';
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
        $this->assertEmpty($projectUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDoNotStoreInvalidMergeRequestData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'pipeline.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'pipeline';
        $hookModel->body['merge_request'] = 'invalid merge_request data';
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
        $this->assertEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($pipelineUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDoNotStoreInvalidBuildData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'pipeline.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'pipeline';
        $hookModel->body['builds'] = ['invalid build data'];
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
        $this->assertEmpty($buildUpdateService->receivedData);
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($pipelineUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDoNotStopForInvalidBuildData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'pipeline.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'pipeline';
        // add an invalid build in front of the others
        array_unshift($hookModel->body['builds'], 'invalid build data');
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

    /**
     * @test
     */
    public function weDoNotStoreInvalidBuildsData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'pipeline.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'pipeline';
        $hookModel->body['builds'] = 'invalid builds data';
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
        $this->assertEmpty($buildUpdateService->receivedData);
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($pipelineUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Services\MergeRequestHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeMergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class MergeRequestHookHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $handler = $this->app->make(MergeRequestHookHandler::class);

        // verify/assert
        $this->assertInstanceOf(MergeRequestHookHandler::class, $handler);
        $this->assertInstanceOf(MergeRequestHookHandlerContract::class, $handler);
    }

    /**
     * @test
     */
    public function weStoreTheMergeRequestData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(
            \Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();

        // run
        $handler =  new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDontStoreInvalidMergeRequestData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(
            \Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $hookModel->body['object_attributes'] = 'invalid merge request data';

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();

        // run
        $handler =  new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDontStoreInvalidProjectData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(
            \Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $hookModel->body['project'] = 'invalid project data';

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();

        // run
        $handler =  new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertEmpty($projectUpdateService->receivedData);
    }
}

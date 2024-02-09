<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Services\MergeRequestHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeMergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeUserUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use function Safe\file_get_contents;
use function Safe\json_decode;

final class MergeRequestHookHandlerTest extends TestCase
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
        $hookModel->body = json_decode(
            file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();
        $userUpdateService = new FakeUserUpdateService();

        // run
        $handler = new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService, $userUpdateService);
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
        $hookModel->body = json_decode(
            file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $hookModel->body['object_attributes'] = 'invalid merge request data';

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();
        $userUpdateService = new FakeUserUpdateService();

        // run
        $handler = new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService, $userUpdateService);
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
        $hookModel->body = json_decode(
            file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $hookModel->body['project'] = 'invalid project data';

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();
        $userUpdateService = new FakeUserUpdateService();

        // run
        $handler = new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService, $userUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertEmpty($projectUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weStoreTheAssignees(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(
            file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $hookModel->body['reviewers'] = [];
        $hookModel->body['assignees'] = [
            [
                'id' => 234,
                'name' => 'assignee name',
                'username' => 'assignee username',
                'avatar_url' => 'http://avatar.example',
            ],
        ];

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();
        $userUpdateService = new FakeUserUpdateService();

        // run
        $handler = new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService, $userUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
        $this->assertNotEmpty($userUpdateService->receivedData);
        $this->assertCount(1, $userUpdateService->receivedData);
        $this->assertEquals($hookModel->body['assignees'], $userUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDontStoreAssigneesIfTheyArentSet(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(
            file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $hookModel->body['reviewers'] = [];
        unset($hookModel->body['assignees']);

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();
        $userUpdateService = new FakeUserUpdateService();

        // run
        $handler = new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService, $userUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
        $this->assertEmpty($userUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weIgnoreInvalidAssignees(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(
            file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $hookModel->body['reviewers'] = [];
        $hookModel->body['assignees'] = 'invalid';

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();
        $userUpdateService = new FakeUserUpdateService();

        // run
        $handler = new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService, $userUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
        $this->assertEmpty($userUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weStoreTheReviewers(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(
            file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $hookModel->body['assignees'] = [];
        $hookModel->body['reviewers'] = [
            [
                'id' => 234,
                'name' => 'assignee name',
                'username' => 'assignee username',
                'avatar_url' => 'http://avatar.example',
            ],
        ];

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();
        $userUpdateService = new FakeUserUpdateService();

        // run
        $handler = new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService, $userUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
        $this->assertNotEmpty($userUpdateService->receivedData);
        $this->assertCount(1, $userUpdateService->receivedData);
        $this->assertEquals($hookModel->body['reviewers'], $userUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDontStoreReviewersIfTheyArentSet(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(
            file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $hookModel->body['assignees'] = [];
        unset($hookModel->body['reviewers']);

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();
        $userUpdateService = new FakeUserUpdateService();

        // run
        $handler = new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService, $userUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
        $this->assertEmpty($userUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weIgnoreInvalidReviewers(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(
            file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $hookModel->body['assignees'] = [];
        $hookModel->body['reviewers'] = 'invalid';

        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $projectUpdateService = new FakeProjectUpdateService();
        $userUpdateService = new FakeUserUpdateService();

        // run
        $handler = new MergeRequestHookHandler($mergeRequestUpdateService, $projectUpdateService, $userUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
        $this->assertEmpty($userUpdateService->receivedData);
    }
}

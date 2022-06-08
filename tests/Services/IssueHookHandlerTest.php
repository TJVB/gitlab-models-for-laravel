<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Services\IssueHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeIssueUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class IssueHookHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $handler = $this->app->make(IssueHookHandler::class);

        // verify/assert
        $this->assertInstanceOf(IssueHookHandler::class, $handler);
        $this->assertInstanceOf(IssueHookHandlerContract::class, $handler);
    }

    /**
     * @test
     */
    public function weStoreTheIssueData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'issue.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'issue';

        $issueUpdateService = new FakeIssueUpdateService();
        $projectUpdateService = new FakeProjectUpdateService();

        // run
        $handler = new IssueHookHandler($issueUpdateService, $projectUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($issueUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
    }
}

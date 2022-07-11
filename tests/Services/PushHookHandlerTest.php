<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\PushHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Services\PushHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class PushHookHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $handler = $this->app->make(PushHookHandler::class);

        // verify/assert
        $this->assertInstanceOf(PushHookHandler::class, $handler);
        $this->assertInstanceOf(PushHookHandlerContract::class, $handler);
    }

    /**
     * @test
     */
    public function weCanStoreThePush(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'push.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'push';
        $projectUpdateService = new FakeProjectUpdateService();

        // run
        $handler = new PushHookHandler($projectUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($projectUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDoNotCrashOnInvalidData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'push.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'push';
        $hookModel->body['project'] = 'invalid project data';
        $projectUpdateService = new FakeProjectUpdateService();

        // run
        $handler = new PushHookHandler($projectUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertEmpty($projectUpdateService->receivedData);
    }
}

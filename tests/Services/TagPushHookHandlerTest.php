<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\TagPushHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Services\TagPushHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeTagUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

final class TagPushHookHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $service = $this->app->make(TagPushHookHandler::class);

        // verify/assert
        $this->assertInstanceOf(TagPushHookHandler::class, $service);
        $this->assertInstanceOf(TagPushHookHandlerContract::class, $service);
    }

    /**
     * @test
     */
    public function weStoreTheTagData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'tag.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'tag';

        $projectUpdateService = new FakeProjectUpdateService();
        $tagUpdateService = new FakeTagUpdateService();

        // run
        $handler = new TagPushHookHandler(
            $projectUpdateService,
            $tagUpdateService,
        );
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($projectUpdateService->receivedData);
        $this->assertNotEmpty($tagUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDoNotStoreInvalidProjectData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'tag.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'tag';
        $hookModel->body['project'] = 'invalid projectdata';
        $projectUpdateService = new FakeProjectUpdateService();
        $tagUpdateService = new FakeTagUpdateService();

        // run
        $handler = new TagPushHookHandler(
            $projectUpdateService,
            $tagUpdateService,
        );
        $handler->handle($hookModel);

        // verify/assert
        $this->assertEmpty($projectUpdateService->receivedData);
        $this->assertNotEmpty($tagUpdateService->receivedData);
    }
}

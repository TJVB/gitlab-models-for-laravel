<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Services\DeploymentHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeDeploymentUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use function Safe\file_get_contents;
use function Safe\json_decode;

final class DeploymentHookHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $handler = $this->app->make(DeploymentHookHandler::class);

        // verify/assert
        $this->assertInstanceOf(DeploymentHookHandler::class, $handler);
        $this->assertInstanceOf(DeploymentHookHandlerContract::class, $handler);
    }

    /**
     * @test
     */
    public function weStoreTheDeploymentData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'deployment.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'deployment';
        $deploymentUpdateService = new FakeDeploymentUpdateService();
        $projectUpdateService = new FakeProjectUpdateService();

        // run
        $handler = new DeploymentHookHandler($deploymentUpdateService, $projectUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($deploymentUpdateService->receivedData);
        $this->assertNotEmpty($projectUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDoNotCrashOnInvalidData(): void
    {
        // setup / mock
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'deployment.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'deployment';

        $hookModel->body['project'] = 'invalid project data';

        $deploymentUpdateService = new FakeDeploymentUpdateService();
        $projectUpdateService = new FakeProjectUpdateService();

        // run
        $handler = new DeploymentHookHandler($deploymentUpdateService, $projectUpdateService);
        $handler->handle($hookModel);

        // verify/assert
        $this->assertNotEmpty($deploymentUpdateService->receivedData);
        $this->assertEmpty($projectUpdateService->receivedData);
    }
}

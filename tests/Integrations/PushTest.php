<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Integrations;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Models\Project;
use TJVB\GitlabModelsForLaravel\Services\PushHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

use function Safe\file_get_contents;
use function Safe\json_decode;

final class PushTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weCanStoreDataFromAPushEvent(): void
    {
        // setup / mock
        $hookBody = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'push.json'), true);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = $hookBody;
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'push';

        // run
        /** @var PushHookHandler $handler */
        $handler = $this->app->make(PushHookHandler::class);
        $handler->handle($hookModel);

        // verify/assert
        // all data is from the push.json example
        $this->assertDatabaseHas(Project::class, [
            'project_id' => $hookBody['project']['id'],
            'name' => $hookBody['project']['name'],
            'web_url' => $hookBody['project']['web_url'],
            'description' => $hookBody['project']['description'],
            'avatar_url' => (string)$hookBody['project']['avatar_url'],
            'visibility_level' => $hookBody['project']['visibility_level'],
        ]);
    }
}

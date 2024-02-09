<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Integrations;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Models\Project;
use TJVB\GitlabModelsForLaravel\Models\Tag;
use TJVB\GitlabModelsForLaravel\Services\TagPushHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use function Safe\file_get_contents;
use function Safe\json_decode;

final class TagTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weCanStoreDataFromATagEvent(): void
    {
        // setup / mock
        $hookBody = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'tag.json'), true);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = $hookBody;
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'tag';

        // run
        /** @var TagPushHookHandler $tagHandler */
        $tagHandler = $this->app->make(TagPushHookHandler::class);
        $tagHandler->handle($hookModel);

        // verify/assert
        // all data is from the tag.json example
        $this->assertDatabaseHas(Tag::class, [
            'project_id' => $hookBody['project_id'],
            'ref' => $hookBody['ref'],
            'checkout_sha' => $hookBody['checkout_sha'],
        ]);
        $this->assertDatabaseHas(Project::class, [
            'project_id' => $hookBody['project']['id'],
            'name' => $hookBody['project']['name'],
            'web_url' => $hookBody['project']['web_url'],
            'description' => $hookBody['project']['description'],
            'avatar_url' => (string) $hookBody['project']['avatar_url'],
            'visibility_level' => $hookBody['project']['visibility_level'],
        ]);
    }
}

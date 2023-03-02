<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Integrations;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Models\MergeRequest;
use TJVB\GitlabModelsForLaravel\Models\Project;
use TJVB\GitlabModelsForLaravel\Services\MergeRequestHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

use function Safe\file_get_contents;
use function Safe\json_decode;

final class MergeRequestTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weCanStoreDataFromTheMergeRequestEvent(): void
    {
        // setup / mock
        $hookBody = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'), true);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = $hookBody;
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';

        // run
        /** @var MergeRequestHookHandler $mergeRequestHandler */
        $mergeRequestHandler = $this->app->make(MergeRequestHookHandler::class);
        $mergeRequestHandler->handle($hookModel);

        // verify/assert
        // all data is from the merge_request.json example
        $this->assertDatabaseHas(MergeRequest::class, [
            'author_id' => $hookBody['object_attributes']['author_id'],
            'blocking_discussions_resolved' => (int) $hookBody['object_attributes']['blocking_discussions_resolved'],
            'description' => $hookBody['object_attributes']['description'],
            'merge_request_id' => $hookBody['object_attributes']['id'],
            'merge_request_iid' => $hookBody['object_attributes']['iid'],
            'merge_status' => $hookBody['object_attributes']['merge_status'],
            'state' => $hookBody['object_attributes']['state'],
            'source_project_id' => $hookBody['object_attributes']['source_project_id'],
            'source_branch' => $hookBody['object_attributes']['source_branch'],
            'target_project_id' => $hookBody['object_attributes']['target_project_id'],
            'target_branch' => $hookBody['object_attributes']['target_branch'],
            'title' => $hookBody['object_attributes']['title'],
            'url' => $hookBody['object_attributes']['url'],
            'work_in_progress' => (int) $hookBody['object_attributes']['work_in_progress'],
        ]);

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

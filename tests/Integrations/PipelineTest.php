<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Integrations;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Models\MergeRequest;
use TJVB\GitlabModelsForLaravel\Models\Pipeline;
use TJVB\GitlabModelsForLaravel\Models\Project;
use TJVB\GitlabModelsForLaravel\Services\PipelineHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

use function Safe\file_get_contents;
use function Safe\json_decode;

final class PipelineTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weStoreTheDataFromThePipelineEvent(): void
    {
        // setup / mock
        $hookBody = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'pipeline.json'), true);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = $hookBody;
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'pipeline';

        // run
        /** @var PipelineHookHandler $pipelineHandler */
        $pipelineHandler = $this->app->make(PipelineHookHandler::class);
        $pipelineHandler->handle($hookModel);

        // verify/assert
        // all data is from the pipeline.json example
        $this->assertDatabaseHas(Pipeline::class, [
            'duration' => $hookBody['object_attributes']['duration'],
            'pipeline_id' => $hookBody['object_attributes']['id'],
            'project_id' => $hookBody['project']['id'],
            'ref' => $hookBody['object_attributes']['ref'],
            'sha' => $hookBody['object_attributes']['sha'],
            'source' => $hookBody['object_attributes']['source'],
            'stages' => \Safe\json_encode($hookBody['object_attributes']['stages']),
            'status' => $hookBody['object_attributes']['status'],
            'tag' => $hookBody['object_attributes']['tag'],
        ]);

        $this->assertDatabaseHas(MergeRequest::class, [
            'merge_request_id' => $hookBody['merge_request']['id'],
            'merge_request_iid' => $hookBody['merge_request']['iid'],
            'merge_status' => $hookBody['merge_request']['merge_status'],
            'state' => $hookBody['merge_request']['state'],
            'source_project_id' => $hookBody['merge_request']['source_project_id'],
            'source_branch' => $hookBody['merge_request']['source_branch'],
            'target_project_id' => $hookBody['merge_request']['target_project_id'],
            'target_branch' => $hookBody['merge_request']['target_branch'],
            'title' => $hookBody['merge_request']['title'],
            'url' => $hookBody['merge_request']['url'],
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

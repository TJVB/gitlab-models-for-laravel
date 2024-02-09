<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Integrations;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Models\Build;
use TJVB\GitlabModelsForLaravel\Services\BuildHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use function Safe\file_get_contents;
use function Safe\json_decode;

final class JobTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weCanStoreDataFromAJobEvent(): void
    {
        // setup / mock
        $hookBody = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'job.json'), true);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = $hookBody;
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'build';

        // run
        /** @var BuildHookHandler $buildHandler */
        $buildHandler = $this->app->make(BuildHookHandler::class);
        $buildHandler->handle($hookModel);

        // verify/assert
        // all data is from the job.json example
        $this->assertDatabaseHas(Build::class, [
            'build_id' => $hookBody['build_id'],
            'pipeline_id' => $hookBody['pipeline_id'],
            'project_id' => $hookBody['project_id'],
            'name' => $hookBody['build_name'],
            'stage' => $hookBody['build_stage'],
            'status' => $hookBody['build_status'],
            'duration' => $hookBody['build_duration'],
            'allow_failure' => $hookBody['build_allow_failure'],
        ]);
    }
}

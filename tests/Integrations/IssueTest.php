<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Integrations;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Models\Issue;
use TJVB\GitlabModelsForLaravel\Models\Project;
use TJVB\GitlabModelsForLaravel\Services\IssueHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

use function Safe\file_get_contents;
use function Safe\json_decode;

final class IssueTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weCanStoreDataFromAnIssueEvent(): void
    {
        // setup / mock
        $hookBody = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'issue.json'), true);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = $hookBody;
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'issue';

        // run
        /** @var IssueHookHandler $issueHandler */
        $issueHandler = $this->app->make(IssueHookHandler::class);
        $issueHandler->handle($hookModel);

        // verify/assert
        // all data is from the issue.json example
        $this->assertDatabaseHas(Issue::class, [
            'issue_id' => $hookBody['object_attributes']['id'],
            'issue_iid' => $hookBody['object_attributes']['iid'],
            'project_id' => $hookBody['object_attributes']['project_id'],
            'title' => $hookBody['object_attributes']['title'],
            'url' => $hookBody['object_attributes']['url'],
            'description' => $hookBody['object_attributes']['description'],
            'state' => $hookBody['object_attributes']['state'],
            'confidential' => $hookBody['object_attributes']['confidential'],
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

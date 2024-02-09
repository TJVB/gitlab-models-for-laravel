<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Integrations;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Models\Issue;
use TJVB\GitlabModelsForLaravel\Models\MergeRequest;
use TJVB\GitlabModelsForLaravel\Models\Note;
use TJVB\GitlabModelsForLaravel\Models\Project;
use TJVB\GitlabModelsForLaravel\Services\NoteHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use function Safe\file_get_contents;
use function Safe\json_decode;

final class NoteTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weCanStoreTheDataForACommentOnCodeSnippet(): void
    {
        config(['gitlab-models.comment_types_to_store' => ['Snippet']]);
        // setup / mock
        $hookBody = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'comment_code_snippet.json'), true);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = $hookBody;
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';

        // run
        /** @var NoteHookHandler $noteHandler */
        $noteHandler = $this->app->make(NoteHookHandler::class);
        $noteHandler->handle($hookModel);

        // verify/assert
        // all data is from the comment_code_snippet.json example
        $this->assertDatabaseHas(Note::class, [
            'author_id' => $hookBody['object_attributes']['author_id'],
            'commit_id' => $hookBody['object_attributes']['commit_id'],
            'line_code' => $hookBody['object_attributes']['line_code'],
            'note' => $hookBody['object_attributes']['note'],
            'note_id' => $hookBody['object_attributes']['id'],
            'noteable_id' => $hookBody['object_attributes']['noteable_id'],
            'noteable_type' => $hookBody['object_attributes']['noteable_type'],
            'project_id' => $hookBody['object_attributes']['project_id'],
            'url' => $hookBody['object_attributes']['url'],
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

    /**
     * @test
     */
    public function weCanStoreTheDataForACommentOnACommit(): void
    {
        config(['gitlab-models.comment_types_to_store' => ['Commit']]);
        // setup / mock
        $hookBody = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'comment_commit.json'), true);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = $hookBody;
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';

        // run
        /** @var NoteHookHandler $noteHandler */
        $noteHandler = $this->app->make(NoteHookHandler::class);
        $noteHandler->handle($hookModel);

        // verify/assert
        // all data is from the comment_code_snippet.json example
        $this->assertDatabaseHas(Note::class, [
            'author_id' => $hookBody['object_attributes']['author_id'],
            'commit_id' => $hookBody['object_attributes']['commit_id'],
            'line_code' => $hookBody['object_attributes']['line_code'],
            'note' => $hookBody['object_attributes']['note'],
            'note_id' => $hookBody['object_attributes']['id'],
            'noteable_id' => $hookBody['object_attributes']['noteable_id'],
            'noteable_type' => $hookBody['object_attributes']['noteable_type'],
            'project_id' => $hookBody['object_attributes']['project_id'],
            'url' => $hookBody['object_attributes']['url'],
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

    /**
     * @test
     */
    public function weCanStoreTheDataForACommentOnAnIssue(): void
    {
        config(['gitlab-models.comment_types_to_store' => ['Issue']]);
        // setup / mock
        $hookBody = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'comment_issue.json'), true);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = $hookBody;
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';

        // run
        /** @var NoteHookHandler $noteHandler */
        $noteHandler = $this->app->make(NoteHookHandler::class);
        $noteHandler->handle($hookModel);

        // verify/assert
        // all data is from the comment_issue.json example
        $this->assertDatabaseHas(Note::class, [
            'author_id' => $hookBody['object_attributes']['author_id'],
            'commit_id' => $hookBody['object_attributes']['commit_id'],
            'line_code' => $hookBody['object_attributes']['line_code'],
            'note' => $hookBody['object_attributes']['note'],
            'note_id' => $hookBody['object_attributes']['id'],
            'noteable_id' => $hookBody['object_attributes']['noteable_id'],
            'noteable_type' => $hookBody['object_attributes']['noteable_type'],
            'project_id' => $hookBody['object_attributes']['project_id'],
            'url' => $hookBody['object_attributes']['url'],
        ]);
        $this->assertDatabaseHas(Issue::class, [
            'issue_id' => $hookBody['issue']['id'],
            'issue_iid' => $hookBody['issue']['iid'],
            'project_id' => $hookBody['issue']['project_id'],
            'title' => $hookBody['issue']['title'],
            'description' => $hookBody['issue']['description'],
            'state' => $hookBody['issue']['state'],
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

    /**
     * @test
     */
    public function weCanStoreTheDataForACommentOnAMergeRequest(): void
    {
        config(['gitlab-models.comment_types_to_store' => ['MergeRequest']]);
        // setup / mock
        $hookBody = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'comment_merge_request.json'), true);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = $hookBody;
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';

        // run
        /** @var NoteHookHandler $noteHandler */
        $noteHandler = $this->app->make(NoteHookHandler::class);
        $noteHandler->handle($hookModel);

        // verify/assert
        // all data is from the comment_merge_request.json example
        $this->assertDatabaseHas(Note::class, [
            'author_id' => $hookBody['object_attributes']['author_id'],
            'commit_id' => $hookBody['object_attributes']['commit_id'],
            'line_code' => $hookBody['object_attributes']['line_code'],
            'note' => $hookBody['object_attributes']['note'],
            'note_id' => $hookBody['object_attributes']['id'],
            'noteable_id' => $hookBody['object_attributes']['noteable_id'],
            'noteable_type' => $hookBody['object_attributes']['noteable_type'],
            'project_id' => $hookBody['object_attributes']['project_id'],
            'url' => $hookBody['object_attributes']['url'],
        ]);
        $this->assertDatabaseHas(MergeRequest::class, [
            'author_id' => $hookBody['merge_request']['author_id'],
            'description' => $hookBody['merge_request']['description'],
            'merge_request_id' => $hookBody['merge_request']['id'],
            'merge_request_iid' => $hookBody['merge_request']['iid'],
            'merge_status' => $hookBody['merge_request']['merge_status'],
            'state' => $hookBody['merge_request']['state'],
            'source_project_id' => $hookBody['merge_request']['source_project_id'],
            'source_branch' => $hookBody['merge_request']['source_branch'],
            'target_project_id' => $hookBody['merge_request']['target_project_id'],
            'target_branch' => $hookBody['merge_request']['target_branch'],
            'title' => $hookBody['merge_request']['title'],
            'url' => $hookBody['object_attributes']['url'],
            'work_in_progress' => (int) $hookBody['merge_request']['work_in_progress'],
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

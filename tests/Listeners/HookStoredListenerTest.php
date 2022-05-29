<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Listeners;

use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateService;
use TJVB\GitlabModelsForLaravel\Listeners\HookStoredListener;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeBuildUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeDeploymentUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeIssueUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeMergeRequestUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeNoteUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakePipelineUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeTagUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitLabWebhooks\Events\HookStored;

class HookStoredListenerTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $listener = $this->app->make(HookStoredListener::class);

        // verify/assert
        $this->assertInstanceOf(GitLabHookStoredListener::class, $listener);
    }

    /**
     * @test
     */
    public function weCanHandleAPushWebhook(): void
    {
        // setup / mock
        $projectUpdater = new FakeProjectUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'push.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'push';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'projectUpdateService' => $projectUpdater,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($projectUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleATagEvent(): void
    {
        // setup / mock
        $projectUpdater = new FakeProjectUpdateService();
        $tagUpdater = new FakeTagUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'tag.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'tag_push';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'projectUpdateService' => $projectUpdater,
            'tagUpdateService' => $tagUpdater,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($projectUpdater->receivedData);
        $this->assertNotEmpty($tagUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleAnIssueEvent(): void
    {
        // setup / mock
        $projectUpdater = new FakeProjectUpdateService();
        $issueUpdater = new FakeIssueUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'issue.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'issue';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'projectUpdateService' => $projectUpdater,
            'issueUpdateService' => $issueUpdater,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($issueUpdater->receivedData);
        $this->assertNotEmpty($projectUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnACommit(): void
    {
        // setup / mock
        $noteUpdater = new FakeNoteUpdateService();
        $projectUpdater = new FakeProjectUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(
            self::EXAMPLE_PAYLOADS . 'comment_commit.json'
        ), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'noteUpdateService' => $noteUpdater,
            'projectUpdateService' => $projectUpdater,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($noteUpdater->receivedData);
        $this->assertNotEmpty($projectUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnAMergeRequest(): void
    {
        // setup / mock
        $mergeRequestUpdate = new FakeMergeRequestUpdateService();
        $noteUpdater = new FakeNoteUpdateService();
        $projectUpdater = new FakeProjectUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(
            self::EXAMPLE_PAYLOADS . 'comment_merge_request.json'
        ), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'mergeRequestUpdateService' => $mergeRequestUpdate,
            'noteUpdateService' => $noteUpdater,
            'projectUpdateService' => $projectUpdater,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdate->receivedData);
        $this->assertNotEmpty($noteUpdater->receivedData);
        $this->assertNotEmpty($projectUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnAnIssue(): void
    {
        // setup / mock
        $issueUpdater = new FakeIssueUpdateService();
        $noteUpdater = new FakeNoteUpdateService();
        $projectUpdater = new FakeProjectUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(
            self::EXAMPLE_PAYLOADS . 'comment_issue.json'
        ), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'issueUpdateService' => $issueUpdater,
            'noteUpdateService' => $noteUpdater,
            'projectUpdateService' => $projectUpdater,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($issueUpdater->receivedData);
        $this->assertNotEmpty($noteUpdater->receivedData);
        $this->assertNotEmpty($projectUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnACodeSnippet(): void
    {
        // setup / mock
        $noteUpdater = new FakeNoteUpdateService();
        $projectUpdater = new FakeProjectUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(
            self::EXAMPLE_PAYLOADS . 'comment_code_snippet.json'
        ), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'noteUpdateService' => $noteUpdater,
            'projectUpdateService' => $projectUpdater,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($noteUpdater->receivedData);
        $this->assertNotEmpty($projectUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleAMergeRequestEvent(): void
    {
        // setup / mock
        $projectUpdater = new FakeProjectUpdateService();
        $mergeRequestUpdate = new FakeMergeRequestUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(
            \Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'projectUpdateService' => $projectUpdater,
            'mergeRequestUpdateService' => $mergeRequestUpdate,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdate->receivedData);
        $this->assertNotEmpty($projectUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleAPipelineEvent(): void
    {
        // setup / mock
        $buildUpdater = new FakeBuildUpdateService();
        $pipelineUpdate = new FakePipelineUpdateService();
        $mergeRequestUpdate = new FakeMergeRequestUpdateService();
        $projectUpdater = new FakeProjectUpdateService();

        /**
         * @var HookStoredListener $listener
         */
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'pipeline.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'pipeline';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'buildUpdateService' => $buildUpdater,
            'pipelineUpdateService' => $pipelineUpdate,
            'mergeRequestUpdateService' => $mergeRequestUpdate,
            'projectUpdateService' => $projectUpdater,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($buildUpdater->receivedData);
        $this->assertNotEmpty($pipelineUpdate->receivedData);
        $this->assertNotEmpty($mergeRequestUpdate->receivedData);
        $this->assertNotEmpty($projectUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleAJobEvent(): void
    {
        // setup / mock
        $buildUpdater = new FakeBuildUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'job.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'build';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'buildUpdateService' => $buildUpdater,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($buildUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleADeploymentEvent(): void
    {
        // setup / mock
        $deploymentUpdater = new FakeDeploymentUpdateService();
        $projectUpdater = new FakeProjectUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'deployment.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'deployment';
        $event = new HookStored($hookModel);

        // run
        $listener = $this->app->make(HookStoredListener::class, [
            'deploymentUpdateService' => $deploymentUpdater,
            'projectUpdateService' => $projectUpdater
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($deploymentUpdater->receivedData);
        $this->assertNotEmpty($projectUpdater->receivedData);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Listeners;

use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateService;
use TJVB\GitlabModelsForLaravel\Listeners\HookStoredListener;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeBuildUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeIssueUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeMergeRequestUpdateService;
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
        $this->app->bind(ProjectUpdateService::class, static function () use ($projectUpdater): ProjectUpdateService {
            return $projectUpdater;
        });
        /**
         * @var HookStoredListener $listener
         */
        $listener = $this->app->make(HookStoredListener::class);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'push.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'push';
        $event = new HookStored($hookModel);

        // run
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
        $this->app->bind(ProjectUpdateService::class, static function () use ($projectUpdater): ProjectUpdateService {
            return $projectUpdater;
        });
        $tagUpdater = new FakeTagUpdateService();
        $this->app->bind(TagUpdateService::class, static function () use ($tagUpdater): TagUpdateService {
            return $tagUpdater;
        });
        /**
         * @var HookStoredListener $listener
         */
        $listener = $this->app->make(HookStoredListener::class);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'tag.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'tag_push';
        $event = new HookStored($hookModel);

        // run
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
        $this->app->bind(ProjectUpdateService::class, static function () use ($projectUpdater): ProjectUpdateService {
            return $projectUpdater;
        });
        $issueUpdater = new FakeIssueUpdateService();
        $this->app->bind(IssueUpdateService::class, static function () use ($issueUpdater): IssueUpdateService {
            return $issueUpdater;
        });
        /**
         * @var HookStoredListener $listener
         */
        $listener = $this->app->make(HookStoredListener::class);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'issue.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'issue';
        $event = new HookStored($hookModel);

        // run
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
        $this->markTestIncomplete('TODO');
        // setup / mock

        // run

        // verify/assert
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnAMergeRequest(): void
    {
        $this->markTestIncomplete('TODO');
        // setup / mock

        // run

        // verify/assert
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnAnIssue(): void
    {
        $this->markTestIncomplete('TODO');
        // setup / mock

        // run

        // verify/assert
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnACodeSnippet(): void
    {
        $this->markTestIncomplete('TODO');
        // setup / mock

        // run

        // verify/assert
    }

    /**
     * @test
     */
    public function weCanHandleAMergeRequestEvent(): void
    {
        // setup / mock
        $projectUpdater = new FakeProjectUpdateService();
        $this->app->bind(ProjectUpdateService::class, static function () use ($projectUpdater): ProjectUpdateService {
            return $projectUpdater;
        });
        $mergeRequestUpdate = new FakeMergeRequestUpdateService();
        $this->app->bind(
            MergeRequestUpdateService::class,
            static function () use ($mergeRequestUpdate): MergeRequestUpdateService {
                return $mergeRequestUpdate;
            }
        );

        /**
         * @var HookStoredListener $listener
         */
        $listener = $this->app->make(HookStoredListener::class);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(
            \Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $event = new HookStored($hookModel);

        // run
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($mergeRequestUpdate->receivedData);
        $this->assertNotEmpty($projectUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleAWikiPageEvent(): void
    {
        $this->markTestIncomplete('TODO');
        // setup / mock

        // run

        // verify/assert
    }

    /**
     * @test
     */
    public function weCanHandleAPipelineEvent(): void
    {
        $this->markTestIncomplete('TODO');
        // setup / mock

        // run

        // verify/assert
    }

    /**
     * @test
     */
    public function weCanHandleAJobEvent(): void
    {
        // setup / mock
        $buildUpdater = new FakeBuildUpdateService();
        $this->app->bind(BuildUpdateService::class, static function () use ($buildUpdater): BuildUpdateService {
            return $buildUpdater;
        });

        /**
         * @var HookStoredListener $listener
         */
        $listener = $this->app->make(HookStoredListener::class);
        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = \Safe\json_decode(\Safe\file_get_contents(self::EXAMPLE_PAYLOADS . 'job.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'build';
        $event = new HookStored($hookModel);

        // run
        $listener->handle($event);

        // verify/assert
        $this->assertNotEmpty($buildUpdater->receivedData);
    }

    /**
     * @test
     */
    public function weCanHandleADeploymentEvent(): void
    {
        $this->markTestIncomplete('TODO');
        // setup / mock

        // run

        // verify/assert
    }
}

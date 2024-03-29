<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Listeners;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\WithFaker;
use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Listeners\HookStoredListener;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeBuildHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeDeploymentHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeIssueHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeMergeRequestHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeNoteHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakePipelineHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakePushHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeTagPushHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitLabWebhooks\Events\HookStored;
use function Safe\file_get_contents;
use function Safe\json_decode;

final class HookStoredListenerTest extends TestCase
{
    use WithFaker;

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
        $pushHookHandler = new FakePushHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'push.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'push';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'pushHookHandler' => $pushHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($pushHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weCanHandleATagEvent(): void
    {
        // setup / mock
        $tagPushHookHandler = new FakeTagPushHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'tag.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'tag_push';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'tagPushHookHandler' => $tagPushHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($tagPushHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weCanHandleAnIssueEvent(): void
    {
        // setup / mock
        $issueHookHandler = new FakeIssueHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'issue.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'issue';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'issueHookHandler' => $issueHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($issueHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnACommit(): void
    {
        // setup / mock
        $noteHookHandler = new FakeNoteHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(
            self::EXAMPLE_PAYLOADS . 'comment_commit.json'
        ), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'noteHookHandler' => $noteHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($noteHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnAMergeRequest(): void
    {
        // setup / mock
        $noteHookHandler = new FakeNoteHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(
            self::EXAMPLE_PAYLOADS . 'comment_merge_request.json'
        ), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'noteHookHandler' => $noteHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($noteHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnAnIssue(): void
    {
        // setup / mock
        $noteHookHandler = new FakeNoteHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(
            self::EXAMPLE_PAYLOADS . 'comment_issue.json'
        ), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'noteHookHandler' => $noteHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($noteHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weCanHandleACommentEventOnACodeSnippet(): void
    {
        // setup / mock
        $noteHookHandler = new FakeNoteHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(
            self::EXAMPLE_PAYLOADS . 'comment_code_snippet.json'
        ), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'noteHookHandler' => $noteHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($noteHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weCanHandleAMergeRequestEvent(): void
    {
        // setup / mock
        $mergeRequestHookHandler = new FakeMergeRequestHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(
            file_get_contents(self::EXAMPLE_PAYLOADS . 'merge_request.json'),
            true
        );
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'merge_request';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'mergeRequestHookHandler' => $mergeRequestHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($mergeRequestHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weCanHandleAPipelineEvent(): void
    {
        // setup / mock
        $pipelineHookHandler = new FakePipelineHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'pipeline.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'pipeline';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'pipelineHookHandler' => $pipelineHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($pipelineHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weCanHandleAJobEvent(): void
    {
        // setup / mock
        $buildHookHandler = new FakeBuildHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'job.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'build';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'buildHookHandler' => $buildHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($buildHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weCanHandleADeploymentEvent(): void
    {
        // setup / mock
        $deploymentHookHandler = new FakeDeploymentHookHandler();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'deployment.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'deployment';
        $event = new HookStored($hookModel);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'deploymentHookHandler' => $deploymentHookHandler,
        ]);
        $listener->handle($event);

        // verify/assert
        $this->assertTrue($deploymentHookHandler->hasReceivedData($hookModel));
    }

    /**
     * @test
     */
    public function weDontCrashOnAnUnknownHookObjectKind(): void
    {
        // setup / mock
        $buildHookHandler = new FakeBuildHookHandler();
        $deploymentHookHandler = new FakeDeploymentHookHandler();
        $issueHookHandler = new FakeIssueHookHandler();
        $mergeRequestHookHandler = new FakeMergeRequestHookHandler();
        $noteHookHandler = new FakeNoteHookHandler();
        $pipelineHookHandler = new FakePipelineHookHandler();
        $pushHookHandler = new FakePushHookHandler();
        $tagPushHookHandler = new FakeTagPushHookHandler();
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = [];
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'unknown';
        $event = new HookStored($hookModel);

        // run
        $listener = new HookStoredListener(
            $buildHookHandler,
            $deploymentHookHandler,
            $issueHookHandler,
            $mergeRequestHookHandler,
            $noteHookHandler,
            $pipelineHookHandler,
            $pushHookHandler,
            $tagPushHookHandler,
        );
        $listener->handle($event);

        // verify/assert
        $this->assertEmpty($buildHookHandler->receivedData);
        $this->assertEmpty($deploymentHookHandler->receivedData);
        $this->assertEmpty($issueHookHandler->receivedData);
        $this->assertEmpty($mergeRequestHookHandler->receivedData);
        $this->assertEmpty($noteHookHandler->receivedData);
        $this->assertEmpty($pipelineHookHandler->receivedData);
        $this->assertEmpty($pushHookHandler->receivedData);
        $this->assertEmpty($tagPushHookHandler->receivedData);
    }

    /**
     * @test
     */
    public function weGetTheQueueConnection(): void
    {
        // setup / mock
        $connection = $this->faker->word();

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.listener_queue.connection', $connection);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'config' => $config,
        ]);
        $result = $listener->viaConnection();

        // verify/assert
        $this->assertSame($connection, $result);
    }

    /**
     * @test
     */
    public function weGetNullAsTheQueueConnection(): void
    {
        // setup / mock
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.listener_queue.connection', null);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'config' => $config,
        ]);
        $result = $listener->viaConnection();

        // verify/assert
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function weGetTheQueueQueue(): void
    {
        // setup / mock
        $queue = $this->faker->word();

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.listener_queue.queue', $queue);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'config' => $config,
        ]);
        $result = $listener->viaQueue();

        // verify/assert
        $this->assertSame($queue, $result);
    }

    /**
     * @test
     */
    public function weGetNullAsTheQueueQueue(): void
    {
        // setup / mock
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.listener_queue.queue', null);

        // run
        /** @var HookStoredListener $listener */
        $listener = $this->app->make(HookStoredListener::class, [
            'config' => $config,
        ]);
        $result = $listener->viaQueue();

        // verify/assert
        $this->assertNull($result);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Listeners;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Queue\ShouldQueue;
use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PushHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagPushHookHandlerContract;
use TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored;

final class HookStoredListener implements GitLabHookStoredListener, ShouldQueue
{
    public function __construct(
        private BuildHookHandlerContract $buildHookHandler,
        private DeploymentHookHandlerContract $deploymentHookHandler,
        private IssueHookHandlerContract $issueHookHandler,
        private MergeRequestHookHandlerContract $mergeRequestHookHandler,
        private NoteHookHandlerContract $noteHookHandler,
        private PipelineHookHandlerContract $pipelineHookHandler,
        private PushHookHandlerContract $pushHookHandler,
        private TagPushHookHandlerContract $tagPushHookHandler,
    ) {
    }

    public function handle(GitLabHookStored $event): void
    {
        $gitLabHookModel = $event->getHook();

        match ($gitLabHookModel->getObjectKind()) {
            'build' => $this->buildHookHandler->handle($gitLabHookModel),
            'deployment' => $this->deploymentHookHandler->handle($gitLabHookModel),
            'issue' => $this->issueHookHandler->handle($gitLabHookModel),
            'merge_request' => $this->mergeRequestHookHandler->handle($gitLabHookModel),
            'note' => $this->noteHookHandler->handle($gitLabHookModel),
            'pipeline' => $this->pipelineHookHandler->handle($gitLabHookModel),
            'push' => $this->pushHookHandler->handle($gitLabHookModel),
            'tag_push' => $this->tagPushHookHandler->handle($gitLabHookModel),
            default => null,
        };
    }

    public function viaConnection(): ?string
    {
        return config('gitlab-models.listener_queue.connection');
    }

    public function viaQueue(): ?string
    {
        return config('gitlab-models.listener_queue.queue');
    }
}

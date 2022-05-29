<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Listeners;

use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateService;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;
use TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

class HookStoredListener implements GitLabHookStoredListener
{
    public function __construct(
        private BuildUpdateService $buildUpdateService,
        private DeploymentUpdateService $deploymentUpdateService,
        private IssueUpdateService $issueUpdateService,
        private MergeRequestUpdateService $mergeRequestUpdateService,
        private NoteUpdateService $noteUpdateService,
        private PipelineUpdateService $pipelineUpdateService,
        private ProjectUpdateService $projectUpdateService,
        private TagUpdateService $tagUpdateService,
    ) {
    }

    public function handle(GitLabHookStored $event): void
    {
        $gitLabHookModel = $event->getHook();

        match ($gitLabHookModel->getObjectKind()) {
            'build' => $this->storeBuildObject($gitLabHookModel),
            'deployment' => $this->storeDeploymentObject($gitLabHookModel),
            'issue' => $this->storeIssueObject($gitLabHookModel),
            'merge_request' => $this->storeMergeRequestObject($gitLabHookModel),
            'note' => $this->storeNoteObject($gitLabHookModel),
            'pipeline' => $this->storePipelineObject($gitLabHookModel),
            'push' => $this->storePushObject($gitLabHookModel),
            'tag_push' => $this->storeTagPushObject($gitLabHookModel),
            default => null,
        };
    }

    private function storeBuildObject(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#job-events
        $this->storeOrUpdateBuildData(BuildDTO::fromBuildEventData($gitLabHookModel->getBody()));
    }

    private function storeDeploymentObject(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#deployment-events
        $body = $gitLabHookModel->getBody();
        $this->storeOrUpdateDeploymentData($body);

        if (isset($body['project']) && is_array($body['project'])) {
            $this->storeOrUpdateProjectData($body['project']);
        }
    }

    private function storeIssueObject(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#issue-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['object_attributes']) && is_array($body['object_attributes'])) {
            $this->storeOrUpdateIssueData($body['object_attributes']);
        }
        if (isset($body['project']) && is_array($body['project'])) {
            $this->storeOrUpdateProjectData($body['project']);
        }
    }

    private function storeMergeRequestObject(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#merge-request-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['object_attributes']) && is_array($body['object_attributes'])) {
            $this->storeOrUpdateMergeRequestData($body['object_attributes']);
        }
        if (isset($body['project']) && is_array($body['project'])) {
            $this->storeOrUpdateProjectData($body['project']);
        }
    }

    private function storeNoteObject(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#comment-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['object_attributes']) && is_array($body['object_attributes'])) {
            $this->storeOrUpdateNoteData($body['object_attributes']);
        }
        if (isset($body['issue']) && is_array($body['issue'])) {
            $this->storeOrUpdateIssueData($body['issue']);
        }
        if (isset($body['merge_request']) && is_array($body['merge_request'])) {
            $this->storeOrUpdateMergeRequestData($body['merge_request']);
        }
        if (isset($body['project']) && is_array($body['project'])) {
            $this->storeOrUpdateProjectData($body['project']);
        }
    }

    private function storePipelineObject(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#pipeline-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['object_attributes']) && is_array($body['object_attributes'])) {
            $this->storeOrUpdatePipelineData($body['object_attributes']);
        }
        if (isset($body['project']) && is_array($body['project'])) {
            $this->storeOrUpdateProjectData($body['project']);
        }
        if (isset($body['merge_request']) && is_array($body['merge_request'])) {
            $this->storeOrUpdateMergeRequestData($body['merge_request']);
        }
        if (isset($body['builds']) && is_array($body['builds'])) {
            $this->storePipelineBuilds($body['builds']);
        }
    }

    private function storePipelineBuilds($builds): void
    {
        foreach ($builds as $build) {
            if (!is_array($build)) {
                continue;
            }
            $this->storeOrUpdateBuildData(BuildDTO::fromPipelineEventData($build));
        }
    }

    private function storePushObject(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#push-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['project']) && is_array($body['project'])) {
            $this->storeOrUpdateProjectData($body['project']);
        }
    }

    private function storeTagPushObject(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#tag-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['project']) && is_array($body['project'])) {
            $this->storeOrUpdateProjectData($body['project']);
        }
        $this->storeOrUpdateTagData($body);
    }

    private function storeOrUpdateBuildData(BuildDTO $buildData): void
    {
        $this->buildUpdateService->updateOrCreate($buildData);
    }

    private function storeOrUpdateDeploymentData(array $deploymentData): void
    {
        $this->deploymentUpdateService->updateOrCreate($deploymentData);
    }

    private function storeOrUpdateProjectData(array $projectData): void
    {
        $this->projectUpdateService->updateOrCreate($projectData);
    }

    private function storeOrUpdateIssueData(array $issueData): void
    {
        $this->issueUpdateService->updateOrCreate($issueData);
    }

    private function storeOrUpdateTagData(array $tagData): void
    {
        $this->tagUpdateService->updateOrCreate($tagData);
    }

    private function storeOrUpdatePipelineData(array $pipelineData): void
    {
        $this->pipelineUpdateService->updateOrCreate($pipelineData);
    }

    private function storeOrUpdateMergeRequestData(array $mergeRequestData): void
    {
        $this->mergeRequestUpdateService->updateOrCreate($mergeRequestData);
    }

    private function storeOrUpdateNoteData(array $noteData): void
    {
        $this->noteUpdateService->updateOrCreate($noteData);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Listeners;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateService;
use TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

class HookStoredListener implements GitLabHookStoredListener
{
    public function __construct(
        private Repository $config,
        private IssueUpdateService $issueUpdateService,
        private ProjectUpdateService $projectUpdateService,
        private TagUpdateService $tagUpdateService,
    ) {
    }

    public function handle(GitLabHookStored $event): void
    {
        $gitLabHookModel = $event->getHook();

        if ($gitLabHookModel->getEventName() === 'push') {
            $this->storePushEvent($gitLabHookModel);
        }
        if ($gitLabHookModel->getEventName() === 'tag_push') {
            $this->storeTagEvent($gitLabHookModel);
        }
        if ($gitLabHookModel->getEventName() === 'issue') {
            $this->storeIssueEvent($gitLabHookModel);
        }
    }

    private function storeIssueEvent(GitLabHookModel $gitLabHookModel): void
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

    private function storePushEvent(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#push-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['project']) && is_array($body['project'])) {
            $this->storeOrUpdateProjectData($body['project']);
        }
    }

    private function storeTagEvent(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#tag-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['project']) && is_array($body['project'])) {
            $this->storeOrUpdateProjectData($body['project']);
        }
        $this->storeOrUpdateTagData($body);
    }

    private function storeOrUpdateProjectData(array $projectData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.projects')) {
            return;
        }
        $this->projectUpdateService->updateOrCreate($projectData);
    }

    private function storeOrUpdateIssueData(array $issueData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.issues')) {
            return;
        }
        $this->issueUpdateService->updateOrCreate($issueData);
    }

    private function storeOrUpdateTagData(array $tagData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.tags')) {
            return;
        }
        $this->tagUpdateService->updateOrCreate($tagData);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Listeners;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService;
use TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

class HookStoredListener implements GitLabHookStoredListener
{
    public function __construct(
        private Repository $config,
        private IssueUpdateService $issueUpdateService,
        private ProjectUpdateService $projectUpdateService
    ) {
    }

    public function handle(GitLabHookStored $event): void
    {
        $gitLabHookModel = $event->getHook();

        if ($gitLabHookModel->getEventName() === 'push') {
            $this->storePushEvent($gitLabHookModel);
        }
        if ($gitLabHookModel->getEventName() === 'issue') {
            $this->storeIssueEvent($gitLabHookModel);
        }
    }

    private function storeIssueEvent(GitLabHookModel $gitLabHookModel): void
    {
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

    private function storeOrUpdateProjectData(array $projectData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.projects')) {
            return;
        }
        $this->projectUpdateService->updateOrCreate($projectData);
    }

    private function storeOrUpdateIssueData(array $issueData): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#issue-events
        if (!$this->config->get('gitlab-models.model_to_store.issues')) {
            return;
        }
        $this->issueUpdateService->updateOrCreate($issueData);
    }
}

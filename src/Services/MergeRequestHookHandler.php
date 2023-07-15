<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\UserUpdateServiceContract;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

final class MergeRequestHookHandler implements MergeRequestHookHandlerContract
{
    public function __construct(
        private MergeRequestUpdateServiceContract $mergeRequestUpdateService,
        private ProjectUpdateServiceContract $projectUpdateService,
        private UserUpdateServiceContract $userUpdateService,
    ) {
    }

    public function handle(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#merge-request-events
        $body = $gitLabHookModel->getBody();

        $this->handleAssignees($body);
        $this->handleReviewers($body);

        if (isset($body['object_attributes']) && is_array($body['object_attributes'])) {
            $this->mergeRequestUpdateService->updateOrCreate($body['object_attributes']);
        }
        if (isset($body['project']) && is_array($body['project'])) {
            $this->projectUpdateService->updateOrCreate($body['project']);
        }
    }

    private function handleAssignees(array $body): void
    {
        if (!isset($body['assignees'])) {
            return;
        }
        if (!is_array($body['assignees'])) {
            return;
        }
        foreach ($body['assignees'] as $assignee) {
            $this->userUpdateService->updateOrCreate($assignee);
        }
    }

    private function handleReviewers(array $body): void
    {
        if (!isset($body['reviewers'])) {
            return;
        }
        if (!is_array($body['reviewers'])) {
            return;
        }
        foreach ($body['reviewers'] as $reviewer) {
            $this->userUpdateService->updateOrCreate($reviewer);
        }
    }
}

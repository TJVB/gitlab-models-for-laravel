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
        if (isset($body['assignees']) && is_array($body['assignees'])) {
            foreach ($body['assignees'] as $assignee) {
                $this->userUpdateService->updateOrCreate($assignee);
            }
        }
        if (isset($body['object_attributes']) && is_array($body['object_attributes'])) {
            $this->mergeRequestUpdateService->updateOrCreate($body['object_attributes']);
        }
        if (isset($body['project']) && is_array($body['project'])) {
            $this->projectUpdateService->updateOrCreate($body['project']);
        }
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateServiceContract;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

final class IssueHookHandler implements IssueHookHandlerContract
{
    public function __construct(
        private IssueUpdateServiceContract $issueUpdateService,
        private ProjectUpdateServiceContract $projectUpdateService,
    ) {
    }

    public function handle(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#issue-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['object_attributes']) && is_array($body['object_attributes'])) {
            $this->issueUpdateService->updateOrCreate($body['object_attributes']);
        }
        if (isset($body['project']) && is_array($body['project'])) {
            $this->projectUpdateService->updateOrCreate($body['project']);
        }
    }
}

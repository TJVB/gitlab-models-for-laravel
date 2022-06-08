<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

final class DeploymentHookHandler implements DeploymentHookHandlerContract
{
    public function __construct(
        private DeploymentUpdateService $deploymentUpdateService,
        private ProjectUpdateService $projectUpdateService,
    ) {
    }

    public function handle(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#deployment-events
        $body = $gitLabHookModel->getBody();
        $this->deploymentUpdateService->updateOrCreate($body);
        if (isset($body['project']) && is_array($body['project'])) {
            $this->projectUpdateService->updateOrCreate($body['project']);
        }
    }
}

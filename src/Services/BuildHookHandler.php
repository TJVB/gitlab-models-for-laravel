<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

final class BuildHookHandler implements BuildHookHandlerContract
{
    public function __construct(private BuildUpdateServiceContract $buildUpdateService,)
    {
    }

    public function handle(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#job-events
        $this->buildUpdateService->updateOrCreate(BuildDTO::fromBuildEventData($gitLabHookModel->getBody()));
    }
}

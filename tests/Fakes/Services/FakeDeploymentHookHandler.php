<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentHookHandlerContract;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

final class FakeDeploymentHookHandler implements DeploymentHookHandlerContract
{
    public array $receivedData = [];
    public function handle(GitLabHookModel $gitLabHookModel): void
    {
        $this->receivedData[] = $gitLabHookModel;
    }

    public function hasReceivedData(GitLabHookModel $gitLabHookModel): bool
    {
        return in_array($gitLabHookModel, $this->receivedData, true);
    }
}

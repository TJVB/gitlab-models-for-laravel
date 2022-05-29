<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentUpdateService;

final class FakeDeploymentUpdateService implements DeploymentUpdateService
{
    public array $receivedData = [];

    public function updateOrCreate(array $deploymentData): void
    {
        $this->receivedData[] = $deploymentData;
    }
}

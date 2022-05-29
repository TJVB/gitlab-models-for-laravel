<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Deployment as DeploymentContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\DeploymentWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Deployment;

final class DeploymentRepository implements DeploymentWriteRepository
{
    public function updateOrCreate(int $deploymentId, array $deploymentData): DeploymentContract
    {
        return Deployment::updateOrCreate(['deployment_id' => $deploymentId], $deploymentData);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Deployment;

interface DeploymentWriteRepository
{
    public function updateOrCreate(int $deploymentId, array $deploymentData): Deployment;
}

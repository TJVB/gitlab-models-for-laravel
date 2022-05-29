<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface DeploymentUpdateService
{
    public function updateOrCreate(array $deploymentData): void;
}

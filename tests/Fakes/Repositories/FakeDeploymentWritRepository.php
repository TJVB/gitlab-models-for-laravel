<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Deployment;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\DeploymentWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Deployment as DeploymentModel;

final class FakeDeploymentWritRepository implements DeploymentWriteRepository
{
    public array $receivedData = [];
    public ?Deployment $result = null;
    public function updateOrCreate(int $deploymentId, array $deploymentData): Deployment
    {
        $this->receivedData[] = [
            'deploymentId' => $deploymentId,
            'deploymentData' => $deploymentData,
        ];
        if ($this->result === null) {
            $this->result = new DeploymentModel();
            $this->result->deployment_id = $deploymentId;
        }
        return $this->result;
    }

    public function hasReceivedData(int $deploymentId, array $deploymentData): bool
    {
        $search = [
            'deploymentId' => $deploymentId,
            'deploymentData' => $deploymentData,
        ];
        return in_array($search, $this->receivedData, true);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\DeploymentWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentUpdateService as DeploymentUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\DeploymentDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class DeploymentUpdateService implements DeploymentUpdateServiceContract
{
    public function __construct(private Repository $config, private DeploymentWriteRepository $repository)
    {
    }

    public function updateOrCreate(array $deploymentData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.deployments')) {
            return;
        }
        if (!isset($deploymentData['deployment_id'])) {
            throw MissingData::missingDataForAction('deployment_id', 'updateOrCreateDeployment');
        }
        $deployment = $this->repository->updateOrCreate($deploymentData['deployment_id'], $deploymentData);
        DeploymentDataReceived::dispatch($deployment);
    }
}

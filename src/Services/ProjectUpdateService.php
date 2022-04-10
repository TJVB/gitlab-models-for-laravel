<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Events\ProjectDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class ProjectUpdateService implements \TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService
{
    public function __construct(
        private Repository $config,
        private ProjectWriteRepository $projectRepository
    ) {
    }

    public function updateOrCreate(array $projectData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.projects')) {
            return;
        }
        if (!isset($projectData['id'])) {
            throw MissingData::missingDataForAction('id', ' updateOrCreateProject');
        }
        $project = $this->projectRepository->updateOrCreate($projectData['id'], $projectData);
        ProjectDataReceived::dispatch($project);
    }
}

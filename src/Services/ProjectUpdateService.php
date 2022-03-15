<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Project;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class ProjectUpdateService implements \TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService
{
    public function __construct(private ProjectWriteRepository $projectRepository)
    {
    }

    public function updateOrCreate(array $projectData): void
    {
        if (!isset($projectData['id'])) {
            throw MissingData::missingDataForAction('id', ' updateOrCreateProject');
        }
        $this->projectRepository->updateOrCreate($projectData['id'], $projectData);
    }
}

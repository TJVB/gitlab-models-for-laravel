<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Project;

interface ProjectWriteRepository
{
    public function updateOrCreate(int $projectId, array $projectData): Project;
}

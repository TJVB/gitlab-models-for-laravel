<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Project;

interface ProjectUpdateService
{
    public function updateOrCreate(array $projectData): Project;
}

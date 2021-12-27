<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Project;
use TJVB\GitlabModelsForLaravel\Exceptions\DataNotFound;

interface ProjectReadRepository
{
    /**
     * @throws DataNotFound
     */
    public function find(int $projectId): Project;
}

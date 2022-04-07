<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Build;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;

interface BuildWriteRepository
{
    public function updateOrCreate(int $buildId, BuildDTO $buildDTO): Build;
}

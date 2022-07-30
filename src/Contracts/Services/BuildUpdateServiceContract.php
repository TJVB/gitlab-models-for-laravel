<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;

interface BuildUpdateServiceContract
{
    public function updateOrCreate(BuildDTO $buildDTO): void;
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface ProjectUpdateServiceContract
{
    public function updateOrCreate(array $projectData): void;
}

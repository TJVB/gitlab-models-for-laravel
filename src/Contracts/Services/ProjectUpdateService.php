<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface ProjectUpdateService
{
    public function updateOrCreate(array $projectData): void;
}

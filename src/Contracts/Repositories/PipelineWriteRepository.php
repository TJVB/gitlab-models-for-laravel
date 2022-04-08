<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Pipeline;

interface PipelineWriteRepository
{
    public function updateOrCreate(int $pipelineId, array $pipelineData): Pipeline;
}

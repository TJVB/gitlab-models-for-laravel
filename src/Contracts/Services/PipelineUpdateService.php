<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface PipelineUpdateService
{
    public function updateOrCreate(array $pipelineData): void;
}

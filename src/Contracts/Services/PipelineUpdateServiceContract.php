<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface PipelineUpdateServiceContract
{
    public function updateOrCreate(array $pipelineData): void;
}

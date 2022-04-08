<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineUpdateService;

class FakePipelineUpdateService implements PipelineUpdateService
{
    public array $receivedData = [];
    public function updateOrCreate(array $pipelineData): void
    {
        $this->receivedData[] = $pipelineData;
    }
}

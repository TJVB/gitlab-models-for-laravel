<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineUpdateServiceContract;

class FakePipelineUpdateService implements PipelineUpdateServiceContract
{
    public array $receivedData = [];
    public function updateOrCreate(array $pipelineData): void
    {
        $this->receivedData[] = $pipelineData;
    }
}

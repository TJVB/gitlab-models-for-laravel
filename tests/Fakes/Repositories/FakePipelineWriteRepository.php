<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Pipeline;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\PipelineWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Pipeline as PipelineModel;

class FakePipelineWriteRepository implements PipelineWriteRepository
{
    public array $receivedData = [];
    public ?Pipeline $result = null;
    public function updateOrCreate(int $pipelineId, array $pipelineData): Pipeline
    {
        $this->receivedData[] = [
            'pipelineId' => $pipelineId,
            'pipelineData' => $pipelineData,
        ];
        if ($this->result === null) {
            $this->result = new PipelineModel();
            $this->result->pipeline_id = $pipelineId;
        }
        return $this->result;
    }

    public function hasReceivedData(int $pipelineId, array $pipelineData): bool
    {
        $search = [
            'pipelineId' => $pipelineId,
            'pipelineData' => $pipelineData,
        ];
        return in_array($search, $this->receivedData, true);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Pipeline as PipelineContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\PipelineWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Pipeline;

final class PipelineRepository implements PipelineWriteRepository
{
    public function updateOrCreate(int $pipelineId, array $pipelineData): PipelineContract
    {
        return Pipeline::updateOrCreate(['pipeline_id' => $pipelineId], [
            'duration' => (int)Arr::get($pipelineData, 'duration'),
            'pipeline_created_at' => CarbonImmutable::make(Arr::get($pipelineData, 'created_at')),
            'pipeline_finished_at' => CarbonImmutable::make(Arr::get($pipelineData, 'finished_at')),
            'project_id' => (int)Arr::get($pipelineData, 'project.id'),
            'ref' => (string) Arr::get($pipelineData, 'ref'),
            'sha' => (string) Arr::get($pipelineData, 'sha'),
            'source' => (string) Arr::get($pipelineData, 'source'),
            'stages' => Arr::get($pipelineData, 'stages', []),
            'status' => (string) Arr::get($pipelineData, 'status'),
            'tag' => Arr::get($pipelineData, 'tag'),
        ]);
    }
}

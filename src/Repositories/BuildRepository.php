<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Build as BuildContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\BuildWriteRepository;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;
use TJVB\GitlabModelsForLaravel\Models\Build;

final class BuildRepository implements BuildWriteRepository
{
    public function updateOrCreate(int $buildId, BuildDTO $buildDTO): BuildContract
    {
        $data = [
            'pipeline_id' => $buildDTO->pipelineId,
            'project_id' => $buildDTO->projectId,
            'name' => $buildDTO->name,
            'stage' => $buildDTO->stage,
            'status' => $buildDTO->status,
            'build_created_at' => $buildDTO->createdAt,
            'started_at' => $buildDTO->startedAt,
            'finished_at' => $buildDTO->finishedAt,
            'allow_failure' => $buildDTO->allowFailure,
        ];
        if ($buildDTO->duration !== null) {
            // this isn't provided with all the events.
            $data['duration'] = $buildDTO->duration;
        }
        return Build::updateOrCreate(['build_id' => $buildId], $data);
    }
}

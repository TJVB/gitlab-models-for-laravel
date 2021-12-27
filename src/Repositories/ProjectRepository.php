<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use Illuminate\Support\Arr;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Project as ProjectContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectReadRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Exceptions\DataNotFound;
use TJVB\GitlabModelsForLaravel\Models\Project;

class ProjectRepository implements ProjectWriteRepository, ProjectReadRepository
{

    public function updateOrCreate(int $projectId, array $projectData): ProjectContract
    {
        return Project::updateOrCreate(['project_id' => $projectId], [
                'name' => Arr::get($projectData, 'name', ''),
                'web_url' => Arr::get($projectData, 'web_url', ''),
                'description' => Arr::get($projectData, 'description', ''),
                'avatar_url' => Arr::get($projectData, 'avatar_url', ''),
                'visibility_level' => Arr::get($projectData, 'visibility_level', 0),
            ]);
    }

    public function find(int $projectId): ProjectContract
    {
        $model = Project::find($projectId);
        if ($model === null) {
            throw DataNotFound::notFoundForModelAndId(ProjectContract::class, $projectId);
        }
        return $model;
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Project;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Project as ProjectModel;

class FakeProjectWriteRepository implements ProjectWriteRepository
{
    public array $receivedData = [];
    public ?Project $result = null;
    public function updateOrCreate(int $projectId, array $projectData): Project
    {
        $this->receivedData[] = [
            'projectId' => $projectId,
            'projectData' => $projectData,
        ];
        if ($this->result === null) {
            $this->result = new ProjectModel();
        }
        return $this->result;
    }

    public function hasReceivedData(int $projectId, array $projectData): bool
    {
        $search = [
            'projectId' => $projectId,
            'projectData' => $projectData,
        ];
        return in_array($search, $this->receivedData, true);
    }
}

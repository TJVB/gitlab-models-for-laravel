<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Build;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\BuildWriteRepository;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;
use TJVB\GitlabModelsForLaravel\Models\Build as BuildModel;
use TJVB\GitlabModelsForLaravel\Models\Issue as IssueModel;

class FakeBuildWriteRepository implements BuildWriteRepository
{
    public array $receivedData = [];
    public ?Build $result = null;
    public function updateOrCreate(int $buildId, BuildDTO $buildDTO): Build
    {
        $this->receivedData[] = [
            'buildId' => $buildId,
            'buildDTO' => $buildDTO,
        ];
        if ($this->result === null) {
            $this->result = new BuildModel();
            $this->result->build_id = $buildId;
        }
        return $this->result;
    }
    public function hasReceivedData(int $buildId, BuildDTO $buildDTO): bool
    {
        $search = [
            'buildId' => $buildId,
            'buildDTO' => $buildDTO,
        ];
        return in_array($search, $this->receivedData, true);
    }
}

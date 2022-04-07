<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue;
use TJVB\GitlabModelsForLaravel\Contracts\Models\MergeRequest;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Issue as IssueModel;
use TJVB\GitlabModelsForLaravel\Models\MergeRequest as MergeRequestModel;

final class FakeMergeRequestWriteRepository implements MergeRequestWriteRepository
{
    public array $receivedData = [];
    public ?MergeRequest $result = null;

    public function updateOrCreate(int $mergeRequestId, array $mergeRequestData): MergeRequest
    {
        $this->receivedData[] = [
            'mergeRequestId' => $mergeRequestId,
            'mergeRequestData' => $mergeRequestData,
        ];
        if ($this->result === null) {
            $this->result = new MergeRequestModel();
        }
        return $this->result;
    }

    public function hasReceivedData(int $mergeRequestId, array $mergeRequestData): bool
    {
        $search = [
            'mergeRequestId' => $mergeRequestId,
            'mergeRequestData' => $mergeRequestData,
        ];
        return in_array($search, $this->receivedData, true);
    }
}

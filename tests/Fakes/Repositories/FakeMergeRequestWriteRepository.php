<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\MergeRequest;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\MergeRequest as MergeRequestModel;

final class FakeMergeRequestWriteRepository implements MergeRequestWriteRepository
{
    public array $receivedData = [];
    public ?MergeRequest $result = null;
    public array $receivedSync = [];
    public ?MergeRequest $syncResult = null;
    public array $receivedAssignees = [];
    public array $receivedReviewers = [];

    public function updateOrCreate(int $mergeRequestId, array $mergeRequestData): MergeRequest
    {
        $this->receivedData[] = [
            'mergeRequestId' => $mergeRequestId,
            'mergeRequestData' => $mergeRequestData,
        ];
        if ($this->result === null) {
            $this->result = new MergeRequestModel();
            $this->result->merge_request_id = $mergeRequestId;
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

    public function syncLabels(int $mergeRequestId, array $labels): ?MergeRequest
    {
        $this->receivedSync[] = [
            'mergeRequestId' => $mergeRequestId,
            'labels' => $labels,
        ];
        return $this->syncResult;
    }

    public function syncAssignees(int $mergeRequestId, array $assigneeIds): void
    {
        $this->receivedAssignees[] = [
            'mergeRequestId' => $mergeRequestId,
            'assigneeIds' => $assigneeIds,
        ];
    }

    public function hasReceivedAssignees(int $mergeRequestId, array $assigneeIds): bool
    {
        $search = [
            'mergeRequestId' => $mergeRequestId,
            'assigneeIds' => $assigneeIds,
        ];
        return in_array($search, $this->receivedAssignees, true);
    }

    public function syncReviewers(int $mergeRequestId, array $reviewerIds): void
    {
        $this->receivedReviewers[] = [
            'mergeRequestId' => $mergeRequestId,
            'reviewerIds' => $reviewerIds,
        ];
    }

    public function hasReceivedReviewers(int $mergeRequestId, array $reviewerIds): bool
    {
        $search = [
            'mergeRequestId' => $mergeRequestId,
            'reviewerIds' => $reviewerIds,
        ];
        return in_array($search, $this->receivedReviewers, true);
    }
}

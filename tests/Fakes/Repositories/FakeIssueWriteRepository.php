<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Issue as IssueModel;

final class FakeIssueWriteRepository implements IssueWriteRepository
{
    public array $receivedData = [];
    public ?Issue $result = null;
    public ?Issue $syncResult = null;

    public array $receivedSync = [];

    public array $receivedSyncAssignees = [];

    public function updateOrCreate(int $issueId, array $issueData): Issue
    {
        $this->receivedData[] = [
            'issueId' => $issueId,
            'issueData' => $issueData,
        ];
        if ($this->result === null) {
            $this->result = new IssueModel();
            $this->result->issue_id = $issueId;
        }
        return $this->result;
    }

    public function hasReceivedData(int $issueId, array $issueData): bool
    {
        $search = [
            'issueId' => $issueId,
            'issueData' => $issueData,
        ];
        return in_array($search, $this->receivedData, true);
    }

    public function syncLabels(int $issueId, array $labels): ?Issue
    {
        $this->receivedSync[] = [
            'issueId' => $issueId,
            'labels' => $labels,
        ];
        return $this->syncResult;
    }

    public function syncAssignees(int $issueId, array $assigneeIds): void
    {
        $this->receivedSyncAssignees[] = [
            'issueId' => $issueId,
            'assigneeIds' => $assigneeIds,
        ];
    }

    public function hasReceivedAssignees(int $issueId, array $assigneeIds): bool
    {
        $search = [
            'issueId' => $issueId,
            'assigneeIds' => $assigneeIds,
        ];
        return in_array($search, $this->receivedSyncAssignees, true);
    }
}

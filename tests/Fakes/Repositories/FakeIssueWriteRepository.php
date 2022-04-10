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
}

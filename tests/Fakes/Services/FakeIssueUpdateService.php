<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService;

class FakeIssueUpdateService implements IssueUpdateService
{
    public array $receivedData = [];
    public function updateOrCreate(array $issueData): void
    {
        $this->receivedData[] = $issueData;
    }
}

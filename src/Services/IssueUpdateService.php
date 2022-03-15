<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class IssueUpdateService implements \TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService
{
    public function __construct(private IssueWriteRepository $writeRepository)
    {
    }

    public function updateOrCreate(array $issueData): void
    {
        if (!isset($issueData['id'])) {
            throw MissingData::missingDataForAction('id', ' updateOrCreateIssue');
        }
        $this->writeRepository->updateOrCreate($issueData['id'], $issueData);
    }
}

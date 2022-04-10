<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Events\IssueDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class IssueUpdateService implements \TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService
{
    public function __construct(
        private Repository $config,
        private IssueWriteRepository $writeRepository
    ) {
    }

    public function updateOrCreate(array $issueData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.issues')) {
            return;
        }
        if (!isset($issueData['id'])) {
            throw MissingData::missingDataForAction('id', ' updateOrCreateIssue');
        }
        $issue = $this->writeRepository->updateOrCreate($issueData['id'], $issueData);
        IssueDataReceived::dispatch($issue);
    }
}

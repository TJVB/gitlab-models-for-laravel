<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\LabelUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\IssueDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class IssueUpdateService implements IssueUpdateServiceContract
{
    public function __construct(
        private Repository $config,
        private IssueWriteRepository $writeRepository,
        private LabelUpdateServiceContract $labelUpdateService,
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
        $this->handleLabels($issueData);
        IssueDataReceived::dispatch($issue);
    }

    private function handleLabels(array $issueData): void
    {
        $labels = [];
        foreach ($issueData['labels'] ?? [] as $labelData) {
            $labels[] = $this->labelUpdateService->updateOrCreate($labelData);
        }
        array_filter($labels);
        $this->writeRepository->syncLabels($issueData['id'], $labels);
    }
}

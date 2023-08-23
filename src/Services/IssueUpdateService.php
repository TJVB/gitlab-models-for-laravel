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
        $this->handleAssignees($issueData);
        $this->handleLabels($issueData);
        IssueDataReceived::dispatch($issue);
    }

    private function handleLabels(array $issueData): void
    {
        if (!$this->config->get('gitlab-models.issue_relations.labels')) {
            return;
        }
        $labels = [];
        foreach ($issueData['labels'] ?? [] as $labelData) {
            $labels[] = $this->labelUpdateService->updateOrCreate($labelData);
        }
        array_filter($labels);
        $this->writeRepository->syncLabels($issueData['id'], $labels);
    }

    private function handleAssignees(array $issueData): void
    {
        if (!$this->config->get('gitlab-models.issue_relations.assignees')) {
            return;
        }
        if (
            !array_key_exists('assignee_ids', $issueData) &&
            !array_key_exists('assignee_id', $issueData)
        ) {
            // for this hook we didn't have any data about assignees
            return;
        }

        $assigneeIds = $this->getBasicAssigneesIds($issueData);
        $this->writeRepository->syncAssignees($issueData['id'], $assigneeIds);
    }

    private function getBasicAssigneesIds(array $issueData): array
    {
        $assigneeIds = [];
        if (isset($issueData['assignee_ids']) && is_array($issueData['assignee_ids'])) {
            $assigneeIds = $issueData['assignee_ids'];
        }
        if (isset($issueData['assignee_id']) && is_numeric($issueData['assignee_id'])) {
            $assigneeIds[] = $issueData['assignee_id'];
        }
        return $assigneeIds;
    }
}

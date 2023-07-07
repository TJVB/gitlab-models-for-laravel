<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\LabelUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\UserUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\MergeRequestDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class MergeRequestUpdateService implements MergeRequestUpdateServiceContract
{
    public function __construct(
        private readonly Repository $config,
        private readonly MergeRequestWriteRepository $writeRepository,
        private readonly LabelUpdateServiceContract $labelUpdateService,
    ) {
    }

    public function updateOrCreate(array $mergeRequestData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.merge_requests')) {
            return;
        }
        if (!isset($mergeRequestData['id'])) {
            throw MissingData::missingDataForAction('id', ' updateOrCreateMergeRequest');
        }
        $mergeRequest = $this->writeRepository->updateOrCreate($mergeRequestData['id'], $mergeRequestData);
        $this->handleAssignees($mergeRequestData);
        $this->handleLabels($mergeRequestData);
        MergeRequestDataReceived::dispatch($mergeRequest);
    }

    private function handleLabels(array $mergeRequestData): void
    {
        if (!$this->config->get('gitlab-models.merge_request_relations.labels')) {
            return;
        }
        $labels = [];
        foreach ($mergeRequestData['labels'] ?? [] as $labelData) {
            $labels[] = $this->labelUpdateService->updateOrCreate($labelData);
        }
        array_filter($labels);
        $this->writeRepository->syncLabels($mergeRequestData['id'], $labels);
    }

    private function handleAssignees(array $mergeRequestData): void
    {
        if (!$this->config->get('gitlab-models.merge_request_relations.assignees')) {
            return;
        }
        if (
            !array_key_exists('assignee', $mergeRequestData) &&
            !array_key_exists('assignees', $mergeRequestData) &&
            !array_key_exists('assignee_id', $mergeRequestData)
        ) {
            // for this hook we didn't have any data about assignees
            return;
        }

        $assigneeIds = $this->getBasicAssigneesIds($mergeRequestData);
        $this->writeRepository->syncAssignees($mergeRequestData['id'], $assigneeIds);
    }

    private function getBasicAssigneesIds(array $mergeRequestData): array
    {
        $assigneeIds = [];
        if (isset($mergeRequestData['assignee_ids']) && is_array($mergeRequestData['assignee_ids'])) {
            $assigneeIds = $mergeRequestData['assignee_ids'];
        }
        if (isset($mergeRequestData['assignee_id']) && is_numeric($mergeRequestData['assignee_id'])) {
            $assigneeIds[] = $mergeRequestData['assignee_id'];
        }
        return $assigneeIds;
    }
}

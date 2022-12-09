<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\LabelUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\MergeRequestDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class MergeRequestUpdateService implements MergeRequestUpdateServiceContract
{
    public function __construct(
        private Repository $config,
        private MergeRequestWriteRepository $writeRepository,
        private LabelUpdateServiceContract $labelUpdateService,
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
        $this->handleLabels($mergeRequestData);
        MergeRequestDataReceived::dispatch($mergeRequest);
    }

    private function handleLabels(array $mergeRequestData): void
    {
        $labels = [];
        foreach ($mergeRequestData['labels'] ?? [] as $labelData) {
            $labels[] = $this->labelUpdateService->updateOrCreate($labelData);
        }
        array_filter($labels);
        $this->writeRepository->syncLabels($mergeRequestData['id'], $labels);
    }
}

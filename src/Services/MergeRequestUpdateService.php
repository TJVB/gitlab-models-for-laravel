<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateService as MergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class MergeRequestUpdateService implements MergeRequestUpdateServiceContract
{
    public function __construct(
        private Repository $config,
        private MergeRequestWriteRepository $writeRepository
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
        $this->writeRepository->updateOrCreate($mergeRequestData['id'], $mergeRequestData);
    }
}

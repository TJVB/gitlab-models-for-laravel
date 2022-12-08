<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\LabelWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\LabelUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\DTOs\LabelDTO;
use TJVB\GitlabModelsForLaravel\Events\LabelDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class LabelUpdateService implements LabelUpdateServiceContract
{
    public function __construct(private Repository $config, private LabelWriteRepository $writeRepository)
    {
    }

    public function updateOrCreate(array $labelData): ?LabelDTO
    {
        if (!$this->config->get('gitlab-models.model_to_store.labels')) {
            return null;
        }
        if (!isset($labelData['id'])) {
            throw MissingData::missingDataForAction('id', ' updateOrCreateLabel');
        }
        $label = $this->writeRepository->updateOrCreate($labelData['id'], $labelData);
        LabelDataReceived::dispatch($label);
        return LabelDTO::fromLabel($label);
    }
}

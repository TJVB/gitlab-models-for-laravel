<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\PipelineWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\PipelineDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class PipelineUpdateService implements PipelineUpdateServiceContract
{
    public function __construct(private Repository $config, private PipelineWriteRepository $writeRepository)
    {
    }

    public function updateOrCreate(array $pipelineData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.pipelines')) {
            return;
        }
        if (!isset($pipelineData['id'])) {
            throw MissingData::missingDataForAction('id', ' updateOrCreatePipeline');
        }
        $pipeline = $this->writeRepository->updateOrCreate($pipelineData['id'], $pipelineData);
        PipelineDataReceived::dispatch($pipeline);
    }
}

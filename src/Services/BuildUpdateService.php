<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\BuildWriteRepository;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;
use TJVB\GitlabModelsForLaravel\Events\BuildDataReceived;

final class BuildUpdateService implements \TJVB\GitlabModelsForLaravel\Contracts\Services\BuildUpdateService
{
    public function __construct(
        private Repository $config,
        private BuildWriteRepository $repository
    ) {
    }

    public function updateOrCreate(BuildDTO $buildDTO): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.builds')) {
            return;
        }
        $build = $this->repository->updateOrCreate($buildDTO->buildId, $buildDTO);
        BuildDataReceived::dispatch($build);
    }
}

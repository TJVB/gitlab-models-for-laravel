<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Repositories\TagWriteRepository;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class TagUpdateService implements \TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateService
{
    public function __construct(private TagWriteRepository $repository)
    {
    }

    public function updateOrCreate(array $tagData): void
    {
        if (!isset($tagData['project_id'])) {
            throw MissingData::missingDataForAction('id', ' updateOrCreateTag');
        }
        if (!isset($tagData['ref'])) {
            throw MissingData::missingDataForAction('ref', ' updateOrCreateTag');
        }
        $this->repository->updateOrCreate(
            $tagData['project_id'],
            $tagData['ref'],
            $tagData
        );
    }
}

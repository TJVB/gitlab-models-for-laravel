<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\MergeRequest;

interface MergeRequestWriteRepository
{
    public function updateOrCreate(int $mergeRequestId, array $mergeRequestData): MergeRequest;

    public function syncLabels(int $mergeRequestId, array $labels): ?MergeRequest;
}

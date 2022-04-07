<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface MergeRequestUpdateService
{
    public function updateOrCreate(array $mergeRequestData): void;
}

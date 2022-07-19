<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface MergeRequestUpdateServiceContract
{
    public function updateOrCreate(array $mergeRequestData): void;
}

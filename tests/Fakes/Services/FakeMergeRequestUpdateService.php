<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateService;

class FakeMergeRequestUpdateService implements MergeRequestUpdateService
{
    public array $receivedData = [];
    public function updateOrCreate(array $mergeRequestData): void
    {
        $this->receivedData[] = $mergeRequestData;
    }
}

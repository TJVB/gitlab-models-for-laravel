<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService;

final class FakeProjectUpdateService implements ProjectUpdateService
{
    public array $receivedData = [];

    public function updateOrCreate(array $projectData): void
    {
        $this->receivedData[] = $projectData;
    }
}

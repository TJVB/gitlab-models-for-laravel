<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateServiceContract;

final class FakeProjectUpdateService implements ProjectUpdateServiceContract
{
    public array $receivedData = [];

    public function updateOrCreate(array $projectData): void
    {
        $this->receivedData[] = $projectData;
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildUpdateService;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;

final class FakeBuildUpdateService implements BuildUpdateService
{
    public array $receivedData = [];

    public function updateOrCreate(BuildDTO $buildDTO): void
    {
        $this->receivedData[] = $buildDTO;
    }
}

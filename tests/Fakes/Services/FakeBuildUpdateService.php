<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;

final class FakeBuildUpdateService implements BuildUpdateServiceContract
{
    public array $receivedData = [];

    public function updateOrCreate(BuildDTO $buildDTO): void
    {
        $this->receivedData[] = $buildDTO;
    }
}

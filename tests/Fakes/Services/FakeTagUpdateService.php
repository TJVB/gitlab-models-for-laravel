<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateServiceContract;

final class FakeTagUpdateService implements TagUpdateServiceContract
{
    public array $receivedData = [];

    public function updateOrCreate(array $tagData): void
    {
        $this->receivedData[] = $tagData;
    }
}

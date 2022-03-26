<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateService;

final class FakeTagUpdateService implements TagUpdateService
{
    public array $receivedData = [];

    public function updateOrCreate(array $tagData): void
    {
        $this->receivedData[] = $tagData;
    }
}

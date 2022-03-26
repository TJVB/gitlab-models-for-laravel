<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Tag;

interface TagWriteRepository
{
    public function updateOrCreate(int $projectId, string $ref, array $tagData): Tag;
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface TagUpdateService
{
    public function updateOrCreate(array $tagData): void;
}

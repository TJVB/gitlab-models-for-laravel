<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface TagUpdateServiceContract
{
    public function updateOrCreate(array $tagData): void;
}

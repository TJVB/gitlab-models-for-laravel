<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface NoteUpdateServiceContract
{
    public function updateOrCreate(array $noteData): void;
}

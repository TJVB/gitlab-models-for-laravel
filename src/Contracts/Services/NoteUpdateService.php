<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface NoteUpdateService
{
    public function updateOrCreate(array $noteData): void;
}

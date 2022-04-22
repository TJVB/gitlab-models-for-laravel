<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Note;

interface NoteWriteRepository
{
    public function updateOrCreate(int $noteId, array $noteData): Note;
}

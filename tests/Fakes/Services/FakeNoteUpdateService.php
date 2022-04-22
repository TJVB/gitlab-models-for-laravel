<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteUpdateService;

final class FakeNoteUpdateService implements NoteUpdateService
{
    public array $receivedData = [];
    public function updateOrCreate(array $noteData): void
    {
        $this->receivedData[] = $noteData;
    }
}

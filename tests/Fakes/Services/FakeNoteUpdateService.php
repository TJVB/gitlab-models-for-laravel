<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteUpdateServiceContract;

final class FakeNoteUpdateService implements NoteUpdateServiceContract
{
    public array $receivedData = [];
    public function updateOrCreate(array $noteData): void
    {
        $this->receivedData[] = $noteData;
    }
}

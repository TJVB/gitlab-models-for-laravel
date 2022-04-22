<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Note;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\NoteWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Note as NoteModel;

class FakeNoteWriteRepository implements NoteWriteRepository
{
    public array $receivedData = [];
    public ?Note $result = null;
    public function updateOrCreate(int $noteId, array $noteData): Note
    {
        $this->receivedData[] = [
            'noteId' => $noteId,
            'noteData' => $noteData,
        ];
        if ($this->result === null) {
            $this->result = new NoteModel();
            $this->result->note_id = $noteId;
        }
        return $this->result;
    }

    public function hasReceivedData(int $noteId, array $noteData): bool
    {
        $search = [
            'noteId' => $noteId,
            'noteData' => $noteData,
        ];
        return in_array($search, $this->receivedData, true);
    }
}

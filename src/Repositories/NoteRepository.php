<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Note as NoteContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\NoteWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Note;

final class NoteRepository implements NoteWriteRepository
{
    public function updateOrCreate(int $noteId, array $noteData): NoteContract
    {
        return Note::updateOrCreate(['note_id' => $noteId], [
            'author_id' => (int) Arr::get($noteData, 'author_id'),
            'commit_id' => Arr::get($noteData, 'commit_id'),
            'line_code' => Arr::get($noteData, 'line_code'),
            'note' => Arr::get($noteData, 'note'),
            'note_created_at' => CarbonImmutable::make(Arr::get($noteData, 'created_at')),
            'note_updated_at' => CarbonImmutable::make(Arr::get($noteData, 'updated_at')),
            'noteable_id' => Arr::get($noteData, 'noteable_id'),
            'noteable_type' => (string) Arr::get($noteData, 'noteable_type'),
            'project_id' => Arr::get($noteData, 'project_id'),
            'url' => (string) Arr::get($noteData, 'url'),
        ]);
    }
}

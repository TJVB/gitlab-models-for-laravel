<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\NoteWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\NoteDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class NoteUpdateService implements NoteUpdateServiceContract
{
    public function __construct(private NoteWriteRepository $repository, private Repository $config)
    {
    }

    public function updateOrCreate(array $noteData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.notes')) {
            return;
        }
        if (!isset($noteData['id']) || !is_numeric($noteData['id'])) {
            throw MissingData::missingDataForAction('id', ' updateOrCreateNote');
        }
        if (!isset($noteData['noteable_type'])) {
            throw MissingData::missingDataForAction('noteable_type', ' updateOrCreateNote');
        }
        if (
            !in_array(
                $noteData['noteable_type'],
                $this->config->get('gitlab-models.comment_types_to_store', []),
                true
            )
        ) {
            return;
        }
        $note = $this->repository->updateOrCreate((int) $noteData['id'], $noteData);
        NoteDataReceived::dispatch($note);
    }
}

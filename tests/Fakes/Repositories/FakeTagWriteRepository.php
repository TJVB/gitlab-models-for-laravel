<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Tag;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\TagWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Tag as TagModel;

final class FakeTagWriteRepository implements TagWriteRepository
{
    public array $receivedData = [];
    public ?Tag $result = null;

    public function updateOrCreate(int $projectId, string $ref, array $tagData): Tag
    {
        $this->receivedData[] = [
            'projectId' => $projectId,
            'ref' => $ref,
            'tagData' => $tagData,
        ];
        if ($this->result === null) {
            $this->result = new TagModel();
        }
        return $this->result;
    }

    public function hasReceivedData(int $projectId, string $ref, array $tagData): bool
    {
        $search = [
            'projectId' => $projectId,
            'ref' => $ref,
            'tagData' => $tagData,
        ];
        return in_array($search, $this->receivedData, true);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use Illuminate\Support\Arr;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Tag as TagContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\TagWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Tag;

final class TagRepository implements TagWriteRepository
{
    public function updateOrCreate(int $projectId, string $ref, array $tagData): TagContract
    {
        return Tag::updateOrCreate(['project_id' => $projectId, 'ref' => $ref], [
            'checkout_sha' => Arr::get($tagData, 'checkout_sha', ''),
        ]);
    }
}

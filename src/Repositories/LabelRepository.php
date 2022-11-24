<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Label as LabelContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\LabelWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Label;

final class LabelRepository implements LabelWriteRepository
{
    public function updateOrCreate(int $labelId, array $labelData): LabelContract
    {
        return Label::updateOrCreate(['label_id' => $labelId], [
            'title' => (string) Arr::get($labelData, 'title'),
            'color' => (string) Arr::get($labelData, 'color'),
            'project_id' => Arr::get($labelData, 'project_id'),
            'label_created_at' => CarbonImmutable::make(Arr::get($labelData, 'created_at')),
            'label_updated_at' => CarbonImmutable::make(Arr::get($labelData, 'updated_at')),
            'description' => (string) Arr::get($labelData, 'description'),
            'type' => Arr::get($labelData, 'type'),
            'group_id' => Arr::get($labelData, 'group_id'),
        ]);
    }
}

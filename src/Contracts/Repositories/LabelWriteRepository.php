<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Label;

interface LabelWriteRepository
{
    public function updateOrCreate(int $labelId, array $labelData): Label;
}

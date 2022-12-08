<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

use TJVB\GitlabModelsForLaravel\DTOs\LabelDTO;

interface LabelUpdateServiceContract
{
    public function updateOrCreate(array $labelData): ?LabelDTO;
}

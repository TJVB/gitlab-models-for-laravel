<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\LabelUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\DTOs\LabelDTO;

final class FakeLabelUpdateService implements LabelUpdateServiceContract
{
    public array $receivedData = [];
    public ?LabelDTO $result = null;
    public function updateOrCreate(array $labelData): ?LabelDTO
    {
        $this->receivedData[] = $labelData;
        return $this->result;
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use Carbon\CarbonImmutable;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Label;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\LabelWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Label as LabelModel;

final class FakeLabelWriteRepository implements LabelWriteRepository
{
    public array $receivedData = [];
    public ?Label $result = null;
    public function updateOrCreate(int $labelId, array $labelData): Label
    {
        $this->receivedData[] = [
            'labelId' => $labelId,
            'labelData' => $labelData,
        ];
        if ($this->result === null) {
            $this->result = new LabelModel();
            $this->result->label_id = $labelId;
            $this->result->title = 'the title';
            $this->result->color = '#ffffff';
            $this->result->label_created_at = CarbonImmutable::now();
            $this->result->label_updated_at = CarbonImmutable::now();
            $this->result->type = 'testtype';
        }
        return $this->result;
    }

    public function hasReceivedData(int $id, array $data): bool
    {
        $search = [
            'labelId' => $id,
            'labelData' => $data,
        ];
        return in_array($search, $this->receivedData, true);
    }
}

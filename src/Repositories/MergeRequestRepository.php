<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use TJVB\GitlabModelsForLaravel\Contracts\Models\MergeRequest as MergeRequestContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\DTOs\LabelDTO;
use TJVB\GitlabModelsForLaravel\Models\Label;
use TJVB\GitlabModelsForLaravel\Models\MergeRequest;

final class MergeRequestRepository implements MergeRequestWriteRepository
{
    public function updateOrCreate(int $mergeRequestId, array $mergeRequestData): MergeRequestContract
    {
        return MergeRequest::updateOrCreate(['merge_request_id' => $mergeRequestId], [
            'author_id' => (int) Arr::get($mergeRequestData, 'author_id'),
            'blocking_discussions_resolved' => Arr::get($mergeRequestData, 'blocking_discussions_resolved', true),
            'description' => (string) Arr::get($mergeRequestData, 'description'),
            'merge_request_created_at' => CarbonImmutable::make(Arr::get($mergeRequestData, 'created_at')),
            'merge_request_iid' => (int) Arr::get($mergeRequestData, 'iid'),
            'merge_status' => (string) Arr::get($mergeRequestData, 'merge_status'),
            'merge_request_updated_at' => CarbonImmutable::make(Arr::get($mergeRequestData, 'updated_at')),
            'state' => (string) Arr::get($mergeRequestData, 'state'),
            'source_project_id' => (int) Arr::get($mergeRequestData, 'source_project_id'),
            'source_branch' => (string) Arr::get($mergeRequestData, 'source_branch'),
            'target_project_id' => (int) Arr::get($mergeRequestData, 'target_project_id'),
            'target_branch' => (string) Arr::get($mergeRequestData, 'target_branch'),
            'title' => (string) Arr::get($mergeRequestData, 'title'),
            'url' => (string) Arr::get($mergeRequestData, 'url'),
            'work_in_progress' => Arr::get($mergeRequestData, 'work_in_progress', false),
        ]);
    }

    public function syncLabels(int $mergeRequestId, array $labels): ?MergeRequestContract
    {
        $labelIds = [];
        foreach ($labels as $label) {
            if ($label instanceof LabelDTO) {
                $labelIds[] = $label->labelId;
            }
        }
        $mergeRequest = MergeRequest::query()
            ->where('merge_request_id', $mergeRequestId)
            ->first();
        if ($mergeRequest === null) {
            return null;
        }
        $mergeRequest->labels()->sync(Label::whereIn('label_id', $labelIds)->get());
        return $mergeRequest;
    }
}

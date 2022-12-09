<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use Illuminate\Support\Arr;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue as IssueContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\DTOs\LabelDTO;
use TJVB\GitlabModelsForLaravel\Models\Issue;
use TJVB\GitlabModelsForLaravel\Models\Label;

final class IssueRepository implements IssueWriteRepository
{
    public function updateOrCreate(int $issueId, array $issueData): IssueContract
    {
        return Issue::updateOrCreate(['issue_id' => $issueId], [
            'issue_iid' => Arr::get($issueData, 'iid', ''),
            'project_id' => Arr::get($issueData, 'project_id', ''),
            'title' => Arr::get($issueData, 'title', ''),
            'description' => Arr::get($issueData, 'description', ''),
            'url' => Arr::get($issueData, 'url', ''),
            'state' => Arr::get($issueData, 'state', ''),
            'confidential' => (bool) Arr::get($issueData, 'confidential', false),
        ]);
    }

    public function syncLabels(int $issueId, array $labels): ?IssueContract
    {
        $labelIds = [];
        foreach ($labels as $label) {
            if ($label instanceof LabelDTO) {
                $labelIds[] = $label->labelId;
            }
        }
        $issue = Issue::query()->where('issue_id', $issueId)->first();
        if ($issue === null) {
            return null;
        }
        $issue->labels()->sync(Label::whereIn('label_id', $labelIds)->get());
        return $issue;
    }
}

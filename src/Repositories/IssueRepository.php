<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use Illuminate\Support\Arr;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue as IssueContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\DTOs\LabelDTO;
use TJVB\GitlabModelsForLaravel\Models\Issue;
use TJVB\GitlabModelsForLaravel\Models\Label;
use TJVB\GitlabModelsForLaravel\Models\User;

final class IssueRepository implements IssueWriteRepository
{
    public function updateOrCreate(int $issueId, array $issueData): IssueContract
    {
        $data = [
            'issue_iid' => Arr::get($issueData, 'iid', ''),
            'project_id' => Arr::get($issueData, 'project_id', ''),
            'title' => Arr::get($issueData, 'title', ''),
            'description' => Arr::get($issueData, 'description', ''),
            'url' => Arr::get($issueData, 'url', ''),
            'state' => Arr::get($issueData, 'state', ''),
        ];
        if (isset($issueData['confidential'])) {
            $data['confidential'] = (bool) Arr::get($issueData, 'confidential');
        }
        return Issue::updateOrCreate(['issue_id' => $issueId], $data);
    }

    public function syncLabels(int $issueId, array $labels): ?IssueContract
    {
        $labelIds = [];
        foreach ($labels as $label) {
            if ($label instanceof LabelDTO) {
                $labelIds[] = $label->labelId;
            }
        }
        /** @var ?Issue $issue */
        $issue = Issue::query()->where('issue_id', $issueId)->first();
        if ($issue === null) {
            return null;
        }
        $issue->labels()->sync(Label::whereIn('label_id', $labelIds)->get());
        return $issue;
    }

    public function syncAssignees(int $issueId, array $assigneeIds): void
    {
        /** @var Issue|null $issue */
        $issue = Issue::query()
            ->where('issue_id', $issueId)
            ->first();
        if ($issue === null) {
            return;
        }
        $issue->assignees()->sync(User::whereIn('user_id', $assigneeIds)->get());
    }
}

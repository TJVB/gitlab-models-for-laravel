<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use Illuminate\Support\Arr;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue as IssueContract;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Issue;

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
}

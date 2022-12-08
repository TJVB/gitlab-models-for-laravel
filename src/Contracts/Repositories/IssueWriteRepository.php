<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue;

interface IssueWriteRepository
{
    public function updateOrCreate(int $issueId, array $issueData): Issue;

    public function syncLabels(int $issueId, array $labels): ?Issue;
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue;

interface IssueUpdateService
{
    public function updateOrCreate(array $issueData): void;
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface IssueUpdateServiceContract
{
    public function updateOrCreate(array $issueData): void;
}

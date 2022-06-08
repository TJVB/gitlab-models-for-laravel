<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

interface IssueHookHandlerContract
{
    public function handle(GitLabHookModel $gitLabHookModel): void;
}

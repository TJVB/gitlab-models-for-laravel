<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Listeners;

use TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored;

interface GitLabHookStoredListener
{
    public function handle(GitLabHookStored $event): void;
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Listeners;

use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored;

class HookStoredListener implements GitLabHookStoredListener
{

    public function handle(GitLabHookStored $event): void
    {
        // TODO: Implement handle() method.
    }
}

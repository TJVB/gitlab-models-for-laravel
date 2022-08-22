<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored;

final class GitlabModelsEventServiceProvider extends EventServiceProvider
{
    public function listens(): array
    {
        $listen = $this->listen;
        foreach (config('gitlab-models.events_to_listen', [GitLabHookStored::class]) as $event) {
            $listen[$event] = [
                GitLabHookStoredListener::class,
            ];
        }
        return $listen;
    }
}

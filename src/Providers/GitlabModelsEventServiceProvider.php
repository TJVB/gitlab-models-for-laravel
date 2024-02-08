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
        // We need to have the implementation for the listener to use the queue if wanted else it will run in sync
        $listener = $this->app->make(GitLabHookStoredListener::class);

        $listen = $this->listen;
        foreach (config('gitlab-models.events_to_listen', [GitLabHookStored::class]) as $event) {
            $listen[$event] = [
                $listener::class,
            ];
        }
        return $listen;
    }
}

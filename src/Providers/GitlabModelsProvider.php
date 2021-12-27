<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Providers;

use Illuminate\Support\ServiceProvider;
use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Project;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectReadRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;

class GitlabModelsProvider extends ServiceProvider
{

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/gitlab-models.php' => \config_path('gitlab-models.php'),
        ], 'config');
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations')
        ], 'migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }



    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/gitlab-models.php', 'gitlab-models');
        $this->app->bind(GitLabHookStoredListener::class, config('gitlab-models.listener'));

        $this->app->bind(Project::class, config('gitlab-models.models.project'));

        $this->app->bind(ProjectReadRepository::class, config('gitlab-models.repositories.project_read'));
        $this->app->bind(ProjectWriteRepository::class, config('gitlab-models.repositories.project_write'));
    }
}

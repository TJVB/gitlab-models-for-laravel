<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Project;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectReadRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\TagWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateService;

class GitlabModelsProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/gitlab-models.php' => \config_path('gitlab-models.php'),
        ], 'config');
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations')
        ], 'migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }



    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/gitlab-models.php', 'gitlab-models');
        $this->app->bind(GitLabHookStoredListener::class, config('gitlab-models.listener'));

        // models
        $this->app->bind(Project::class, config('gitlab-models.models.project'));

        // repositories
        $this->app->bind(IssueWriteRepository::class, config('gitlab-models.repositories.issue_read'));
        $this->app->bind(ProjectReadRepository::class, config('gitlab-models.repositories.project_read'));
        $this->app->bind(ProjectWriteRepository::class, config('gitlab-models.repositories.project_write'));
        $this->app->bind(TagWriteRepository::class, config('gitlab-models.repositories.tag_write'));

        // services
        $this->app->bind(IssueUpdateService::class, config('gitlab-models.services.issue_update'));
        $this->app->bind(ProjectUpdateService::class, config('gitlab-models.services.project_update'));
        $this->app->bind(TagUpdateService::class, config('gitlab-models.services.tag_update'));
    }

    public function provides()
    {
        return [
            GitLabHookStoredListener::class,

            // repositories
            IssueWriteRepository::class,
            ProjectReadRepository::class,
            ProjectWriteRepository::class,
            TagWriteRepository::class,

            // services
            IssueUpdateService::class,
            ProjectUpdateService::class,
            TagUpdateService::class,
        ];
    }
}

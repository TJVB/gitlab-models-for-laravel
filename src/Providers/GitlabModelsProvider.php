<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Providers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\BuildWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\DeploymentWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\NoteWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\PipelineWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectReadRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\TagWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PushHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagPushHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateServiceContract;
use TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/gitlab-models.php', 'gitlab-models');
        $this->app->bind(GitLabHookStoredListener::class, config('gitlab-models.listener'));
        $this->app->register(GitlabModelsEventServiceProvider::class);

        $this->registerRepositories();
        $this->registerServices();
    }

    public function provides(): array
    {
        return [
            GitLabHookStoredListener::class,

            // repositories
            BuildWriteRepository::class,
            DeploymentWriteRepository::class,
            IssueWriteRepository::class,
            MergeRequestWriteRepository::class,
            NoteWriteRepository::class,
            PipelineWriteRepository::class,
            ProjectReadRepository::class,
            ProjectWriteRepository::class,
            TagWriteRepository::class,

            // services
            // hook handler services
            BuildHookHandlerContract::class,
            DeploymentHookHandlerContract::class,
            IssueHookHandlerContract::class,
            MergeRequestHookHandlerContract::class,
            NoteHookHandlerContract::class,
            PipelineHookHandlerContract::class,
            PushHookHandlerContract::class,
            TagPushHookHandlerContract::class,

            // update services
            BuildUpdateServiceContract::class,
            DeploymentUpdateService::class,
            IssueUpdateServiceContract::class,
            MergeRequestUpdateServiceContract::class,
            NoteUpdateServiceContract::class,
            PipelineUpdateServiceContract::class,
            ProjectUpdateServiceContract::class,
            TagUpdateServiceContract::class,
        ];
    }

    private function registerRepositories(): void
    {
        $this->app->bind(BuildWriteRepository::class, config('gitlab-models.repositories.build_write'));
        $this->app->bind(DeploymentWriteRepository::class, config('gitlab-models.repositories.deployment_write'));
        $this->app->bind(IssueWriteRepository::class, config('gitlab-models.repositories.issue_write'));
        $this->app->bind(MergeRequestWriteRepository::class, config('gitlab-models.repositories.merge_request_write'));
        $this->app->bind(NoteWriteRepository::class, config('gitlab-models.repositories.note_write'));
        $this->app->bind(PipelineWriteRepository::class, config('gitlab-models.repositories.pipeline_write'));
        $this->app->bind(ProjectReadRepository::class, config('gitlab-models.repositories.project_read'));
        $this->app->bind(ProjectWriteRepository::class, config('gitlab-models.repositories.project_write'));
        $this->app->bind(TagWriteRepository::class, config('gitlab-models.repositories.tag_write'));
    }

    private function registerServices(): void
    {
        // hook handler services
        $this->app->bind(BuildHookHandlerContract::class, config('gitlab-models.services.build_handler'));
        $this->app->bind(DeploymentHookHandlerContract::class, config('gitlab-models.services.deployment_handler'));
        $this->app->bind(IssueHookHandlerContract::class, config('gitlab-models.services.issue_handler'));
        $this->app->bind(
            MergeRequestHookHandlerContract::class,
            config('gitlab-models.services.merge_request_handler')
        );
        $this->app->bind(NoteHookHandlerContract::class, config('gitlab-models.services.note_handler'));
        $this->app->bind(PipelineHookHandlerContract::class, config('gitlab-models.services.pipeline_handler'));
        $this->app->bind(PushHookHandlerContract::class, config('gitlab-models.services.push_handler'));
        $this->app->bind(TagPushHookHandlerContract::class, config('gitlab-models.services.tag_push_handler'));

        // update services
        $this->app->bind(BuildUpdateServiceContract::class, config('gitlab-models.services.build_update'));
        $this->app->bind(DeploymentUpdateService::class, config('gitlab-models.services.deployment_update'));
        $this->app->bind(NoteUpdateServiceContract::class, config('gitlab-models.services.note_update'));
        $this->app->bind(IssueUpdateServiceContract::class, config('gitlab-models.services.issue_update'));
        $this->app->bind(
            MergeRequestUpdateServiceContract::class,
            config('gitlab-models.services.merge_request_update')
        );
        $this->app->bind(PipelineUpdateServiceContract::class, config('gitlab-models.services.pipeline_update'));
        $this->app->bind(ProjectUpdateServiceContract::class, config('gitlab-models.services.project_update'));
        $this->app->bind(TagUpdateServiceContract::class, config('gitlab-models.services.tag_update'));
    }
}

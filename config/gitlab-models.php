<?php

return [

    'model_to_store' => [
        'issues' => env('GITLAB_MODELS_STORE_ISSUES', true),
        'projects' => env('GITLAB_MODELS_STORE_PROJECTS', true),
        'tags' => env('GITLAB_MODELS_STORE_TAGS', true),
    ],

    'events_to_listen' => [
        \TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored::class,
    ],

    'listener' => \TJVB\GitlabModelsForLaravel\Listeners\HookStoredListener::class,

    'models' => [
        'project' => \TJVB\GitlabModelsForLaravel\Models\Project::class,
    ],

    'repositories' => [
        'issue_read' => \TJVB\GitlabModelsForLaravel\Repositories\IssueRepository::class,
        'project_read' => \TJVB\GitlabModelsForLaravel\Repositories\ProjectRepository::class,
        'project_write' => \TJVB\GitlabModelsForLaravel\Repositories\ProjectRepository::class,
        'tag_write' => \TJVB\GitlabModelsForLaravel\Repositories\TagRepository::class,
    ],

    'services' => [
        'issue_update' => \TJVB\GitlabModelsForLaravel\Services\IssueUpdateService::class,
        'project_update' => \TJVB\GitlabModelsForLaravel\Services\ProjectUpdateService::class,
        'tag_update' => \TJVB\GitlabModelsForLaravel\Services\TagUpdateService::class,
    ],
];

<?php

return [

    'model_to_store' => [
        'builds' => env('GITLAB_MODELS_STORE_BUILDS', true),
        'deployments' => env('GITLAB_MODELS_STORE_DEPLOYMENTS', true),
        'issues' => env('GITLAB_MODELS_STORE_ISSUES', true),
        'merge_requests' => env('GITLAB_MODELS_STORE_MERGE_REQUESTS', true),
        'notes' => env('GITLAB_MODELS_STORE_NOTES', true),
        'pipelines' => env('GITLAB_MODELS_STORE_PIPELINES', true),
        'projects' => env('GITLAB_MODELS_STORE_PROJECTS', true),
        'tags' => env('GITLAB_MODELS_STORE_TAGS', true),
    ],

    'comment_types_to_store' => [
//        'Commit',
        'MergeRequest',
        'Issue',
//        'Snippet',
    ],

    'events_to_listen' => [
        \TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored::class,
    ],

    'listener' => \TJVB\GitlabModelsForLaravel\Listeners\HookStoredListener::class,

    'repositories' => [
        'build_write' => \TJVB\GitlabModelsForLaravel\Repositories\BuildRepository::class,
        'deployment_write' => \TJVB\GitlabModelsForLaravel\Repositories\DeploymentRepository::class,
        'issue_write' => \TJVB\GitlabModelsForLaravel\Repositories\IssueRepository::class,
        'merge_request_write' => \TJVB\GitlabModelsForLaravel\Repositories\MergeRequestRepository::class,
        'note_write' => \TJVB\GitlabModelsForLaravel\Repositories\NoteRepository::class,
        'pipeline_write' => \TJVB\GitlabModelsForLaravel\Repositories\PipelineRepository::class,
        'project_read' => \TJVB\GitlabModelsForLaravel\Repositories\ProjectRepository::class,
        'project_write' => \TJVB\GitlabModelsForLaravel\Repositories\ProjectRepository::class,
        'tag_write' => \TJVB\GitlabModelsForLaravel\Repositories\TagRepository::class,
    ],

    'services' => [
        'build_handler' => \TJVB\GitlabModelsForLaravel\Services\BuildHookHandler::class,
        'deployment_handler' => \TJVB\GitlabModelsForLaravel\Services\DeploymentHookHandler::class,
        'issue_handler' => \TJVB\GitlabModelsForLaravel\Services\IssueHookHandler::class,
        'merge_request_handler' => \TJVB\GitlabModelsForLaravel\Services\MergeRequestHookHandler::class,
        'note_handler' => \TJVB\GitlabModelsForLaravel\Services\NoteHookHandler::class,
        'pipeline_handler' => \TJVB\GitlabModelsForLaravel\Services\PipelineHookHandler::class,
        'push_handler' => \TJVB\GitlabModelsForLaravel\Services\PushHookHandler::class,
        'tag_push_handler' => \TJVB\GitlabModelsForLaravel\Services\TagPushHookHandler::class,

        'build_update' => \TJVB\GitlabModelsForLaravel\Services\BuildUpdateService::class,
        'deployment_update' => \TJVB\GitlabModelsForLaravel\Services\DeploymentUpdateService::class,
        'issue_update' => \TJVB\GitlabModelsForLaravel\Services\IssueUpdateService::class,
        'merge_request_update' => \TJVB\GitlabModelsForLaravel\Services\MergeRequestUpdateService::class,
        'note_update' => \TJVB\GitlabModelsForLaravel\Services\NoteUpdateService::class,
        'pipeline_update' => \TJVB\GitlabModelsForLaravel\Services\PipelineUpdateService::class,
        'project_update' => \TJVB\GitlabModelsForLaravel\Services\ProjectUpdateService::class,
        'tag_update' => \TJVB\GitlabModelsForLaravel\Services\TagUpdateService::class,
    ],
];

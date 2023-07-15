<?php

return [

    /**
     * These are the GitLab models that we want to store in the database.
     * We will not call the save function in the repository if it is disabled
     */
    'model_to_store' => [
        'builds' => env('GITLAB_MODELS_STORE_BUILDS', true),
        'deployments' => env('GITLAB_MODELS_STORE_DEPLOYMENTS', true),
        'issues' => env('GITLAB_MODELS_STORE_ISSUES', true),
        'labels' => env('GITLAB_MODELS_STORE_LABELS', true),
        'merge_requests' => env('GITLAB_MODELS_STORE_MERGE_REQUESTS', true),
        'notes' => env('GITLAB_MODELS_STORE_NOTES', true),
        'pipelines' => env('GITLAB_MODELS_STORE_PIPELINES', true),
        'projects' => env('GITLAB_MODELS_STORE_PROJECTS', true),
        'tags' => env('GITLAB_MODELS_STORE_TAGS', true),
        'users' => env('GITLAB_MODELS_STORE_USERS', true),
    ],

    /**
     * A comment has a type that explains where it is connected.
     * By default, we only want to store the comments on a merge requests and issues.
     */
    'comment_types_to_store' => [
//        'Commit',
        'MergeRequest',
        'Issue',
//        'Snippet',
    ],

    'merge_request_relations' => [
        'assignees' => env('GITLAB_MODELS_STORE_MERGE_REQUESTS_ASSIGNEES', true),
        'labels' => env('GITLAB_MODELS_STORE_MERGE_REQUESTS_LABELS', true),
        'reviewers' => env('GITLAB_MODELS_STORE_MERGE_REQUESTS_REVIEWERS', true),
    ],

    /**
     * The event(s) that we handle to start storing the information
     * This need to implement the GitLabHookStored interface
     */
    'events_to_listen' => [
        \TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored::class,
    ],

    /**
     * The listener that listen to the event(s)
     * This need to implement the TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener interface
     */
    'listener' => \TJVB\GitlabModelsForLaravel\Listeners\HookStoredListener::class,

    /**
     * These are the bindings for the repositories, these need to implement the corresponding interfaces.
     */
    'repositories' => [
        'build_write' => \TJVB\GitlabModelsForLaravel\Repositories\BuildRepository::class,
        'deployment_write' => \TJVB\GitlabModelsForLaravel\Repositories\DeploymentRepository::class,
        'issue_write' => \TJVB\GitlabModelsForLaravel\Repositories\IssueRepository::class,
        'label_write' => \TJVB\GitlabModelsForLaravel\Repositories\LabelRepository::class,
        'merge_request_write' => \TJVB\GitlabModelsForLaravel\Repositories\MergeRequestRepository::class,
        'note_write' => \TJVB\GitlabModelsForLaravel\Repositories\NoteRepository::class,
        'pipeline_write' => \TJVB\GitlabModelsForLaravel\Repositories\PipelineRepository::class,
        'project_read' => \TJVB\GitlabModelsForLaravel\Repositories\ProjectRepository::class,
        'project_write' => \TJVB\GitlabModelsForLaravel\Repositories\ProjectRepository::class,
        'tag_write' => \TJVB\GitlabModelsForLaravel\Repositories\TagRepository::class,
        'user_write' => \TJVB\GitlabModelsForLaravel\Repositories\UserRepository::class,
    ],

    /**
     * These are the bindings for the services, these need to implement the corresponding interfaces.
     */
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
        'label_update' => \TJVB\GitlabModelsForLaravel\Services\LabelUpdateService::class,
        'merge_request_update' => \TJVB\GitlabModelsForLaravel\Services\MergeRequestUpdateService::class,
        'note_update' => \TJVB\GitlabModelsForLaravel\Services\NoteUpdateService::class,
        'pipeline_update' => \TJVB\GitlabModelsForLaravel\Services\PipelineUpdateService::class,
        'project_update' => \TJVB\GitlabModelsForLaravel\Services\ProjectUpdateService::class,
        'tag_update' => \TJVB\GitlabModelsForLaravel\Services\TagUpdateService::class,
        'user_update' => \TJVB\GitlabModelsForLaravel\Services\UserUpdateService::class,
    ],
];

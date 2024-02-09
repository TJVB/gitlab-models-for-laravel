<?php

declare(strict_types=1);

use TJVB\GitlabModelsForLaravel\Listeners\HookStoredListener;
use TJVB\GitlabModelsForLaravel\Repositories\BuildRepository;
use TJVB\GitlabModelsForLaravel\Repositories\DeploymentRepository;
use TJVB\GitlabModelsForLaravel\Repositories\IssueRepository;
use TJVB\GitlabModelsForLaravel\Repositories\LabelRepository;
use TJVB\GitlabModelsForLaravel\Repositories\MergeRequestRepository;
use TJVB\GitlabModelsForLaravel\Repositories\NoteRepository;
use TJVB\GitlabModelsForLaravel\Repositories\PipelineRepository;
use TJVB\GitlabModelsForLaravel\Repositories\ProjectRepository;
use TJVB\GitlabModelsForLaravel\Repositories\TagRepository;
use TJVB\GitlabModelsForLaravel\Repositories\UserRepository;
use TJVB\GitlabModelsForLaravel\Services\BuildHookHandler;
use TJVB\GitlabModelsForLaravel\Services\BuildUpdateService;
use TJVB\GitlabModelsForLaravel\Services\DeploymentHookHandler;
use TJVB\GitlabModelsForLaravel\Services\DeploymentUpdateService;
use TJVB\GitlabModelsForLaravel\Services\IssueHookHandler;
use TJVB\GitlabModelsForLaravel\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Services\LabelUpdateService;
use TJVB\GitlabModelsForLaravel\Services\MergeRequestHookHandler;
use TJVB\GitlabModelsForLaravel\Services\MergeRequestUpdateService;
use TJVB\GitlabModelsForLaravel\Services\NoteHookHandler;
use TJVB\GitlabModelsForLaravel\Services\NoteUpdateService;
use TJVB\GitlabModelsForLaravel\Services\PipelineHookHandler;
use TJVB\GitlabModelsForLaravel\Services\PipelineUpdateService;
use TJVB\GitlabModelsForLaravel\Services\ProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Services\PushHookHandler;
use TJVB\GitlabModelsForLaravel\Services\TagPushHookHandler;
use TJVB\GitlabModelsForLaravel\Services\TagUpdateService;
use TJVB\GitlabModelsForLaravel\Services\UserUpdateService;
use TJVB\GitLabWebhooks\Contracts\Events\GitLabHookStored;

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

    'issue_relations' => [
        'assignees' => env('GITLAB_MODELS_STORE_ISSUES_ASSIGNEES', true),
        'labels' => env('GITLAB_MODELS_STORE_ISSUES_LABELS', true),
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
        GitLabHookStored::class,
    ],

    /**
     * The listener that listen to the event(s)
     * This need to implement the TJVB\GitlabModelsForLaravel\Contracts\Listeners\GitLabHookStoredListener interface
     */
    'listener' => HookStoredListener::class,

    'listener_queue' => [
        'connection' => env('GITLAB_MODELS_QUEUE_CONNECTION'),
        'queue' => env('GITLAB_MODELS_QUEUE_QUEUE'),
    ],

    /**
     * These are the bindings for the repositories, these need to implement the corresponding interfaces.
     */
    'repositories' => [
        'build_write' => BuildRepository::class,
        'deployment_write' => DeploymentRepository::class,
        'issue_write' => IssueRepository::class,
        'label_write' => LabelRepository::class,
        'merge_request_write' => MergeRequestRepository::class,
        'note_write' => NoteRepository::class,
        'pipeline_write' => PipelineRepository::class,
        'project_read' => ProjectRepository::class,
        'project_write' => ProjectRepository::class,
        'tag_write' => TagRepository::class,
        'user_write' => UserRepository::class,
    ],

    /**
     * These are the bindings for the services, these need to implement the corresponding interfaces.
     */
    'services' => [
        'build_handler' => BuildHookHandler::class,
        'deployment_handler' => DeploymentHookHandler::class,
        'issue_handler' => IssueHookHandler::class,
        'merge_request_handler' => MergeRequestHookHandler::class,
        'note_handler' => NoteHookHandler::class,
        'pipeline_handler' => PipelineHookHandler::class,
        'push_handler' => PushHookHandler::class,
        'tag_push_handler' => TagPushHookHandler::class,

        'build_update' => BuildUpdateService::class,
        'deployment_update' => DeploymentUpdateService::class,
        'issue_update' => IssueUpdateService::class,
        'label_update' => LabelUpdateService::class,
        'merge_request_update' => MergeRequestUpdateService::class,
        'note_update' => NoteUpdateService::class,
        'pipeline_update' => PipelineUpdateService::class,
        'project_update' => ProjectUpdateService::class,
        'tag_update' => TagUpdateService::class,
        'user_update' => UserUpdateService::class,
    ],
];

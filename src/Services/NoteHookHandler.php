<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

final class NoteHookHandler implements NoteHookHandlerContract
{
    public function __construct(
        private IssueUpdateService $issueUpdateService,
        private MergeRequestUpdateServiceContract $mergeRequestUpdateService,
        private NoteUpdateService $noteUpdateService,
        private ProjectUpdateService $projectUpdateService,
    ) {
    }

    public function handle(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#comment-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['object_attributes']) && is_array($body['object_attributes'])) {
            $this->noteUpdateService->updateOrCreate($body['object_attributes']);
        }
        if (isset($body['issue']) && is_array($body['issue'])) {
            $this->issueUpdateService->updateOrCreate($body['issue']);
        }
        if (isset($body['merge_request']) && is_array($body['merge_request'])) {
            $this->mergeRequestUpdateService->updateOrCreate($body['merge_request']);
        }
        if (isset($body['project']) && is_array($body['project'])) {
            $this->projectUpdateService->updateOrCreate($body['project']);
        }
    }
}

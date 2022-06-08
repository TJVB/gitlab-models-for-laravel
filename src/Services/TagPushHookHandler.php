<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagPushHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateService;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

final class TagPushHookHandler implements TagPushHookHandlerContract
{
    public function __construct(
        private ProjectUpdateService $projectUpdateService,
        private TagUpdateService $tagUpdateService,
    ) {
    }

    public function handle(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#tag-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['project']) && is_array($body['project'])) {
            $this->projectUpdateService->updateOrCreate($body['project']);
        }
        $this->tagUpdateService->updateOrCreate($body);
    }
}

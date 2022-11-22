<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;
use TJVB\GitLabWebhooks\Contracts\Models\GitLabHookModel;

final class PipelineHookHandler implements PipelineHookHandlerContract
{
    public function __construct(
        private BuildUpdateServiceContract $buildUpdateService,
        private MergeRequestUpdateServiceContract $mergeRequestUpdateService,
        private PipelineUpdateServiceContract $pipelineUpdateService,
        private ProjectUpdateServiceContract $projectUpdateService,
    ) {
    }

    public function handle(GitLabHookModel $gitLabHookModel): void
    {
        // https://docs.gitlab.com/ee/user/project/integrations/webhook_events.html#pipeline-events
        $body = $gitLabHookModel->getBody();
        if (isset($body['object_attributes']) && is_array($body['object_attributes'])) {
            $body['object_attributes']['project'] = $body['project'] ?? null;
            $this->pipelineUpdateService->updateOrCreate($body['object_attributes']);
        }
        if (isset($body['project']) && is_array($body['project'])) {
            $this->projectUpdateService->updateOrCreate($body['project']);
        }
        if (isset($body['merge_request']) && is_array($body['merge_request'])) {
            $this->mergeRequestUpdateService->updateOrCreate($body['merge_request']);
        }
        $this->handleBuilds($body);
    }

    private function handleBuilds(array $body): void
    {
        if (!isset($body['builds']) || !is_array($body['builds'])) {
            return;
        }
        foreach ($body['builds'] as $build) {
            if (!is_array($build)) {
                continue;
            }
            $this->buildUpdateService->updateOrCreate(BuildDTO::fromPipelineEventData($build));
        }
    }
}

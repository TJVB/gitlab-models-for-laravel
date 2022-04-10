<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Project;

final class IssueDataReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;


    public function __construct(public Issue $issue)
    {
    }

    public function getProject(): Issue
    {
        return $this->issue;
    }
}

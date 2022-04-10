<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Project;

final class ProjectDataReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;


    public function __construct(public Project $project)
    {
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}

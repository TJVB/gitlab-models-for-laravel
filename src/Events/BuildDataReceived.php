<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Build;

final class BuildDataReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Build $build)
    {
    }

    public function getBuild(): Build
    {
        return $this->build;
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use TJVB\GitlabModelsForLaravel\Contracts\Models\MergeRequest;

final class MergeRequestDataReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public MergeRequest $mergeRequest)
    {
    }

    public function getMergeRequest(): MergeRequest
    {
        return $this->mergeRequest;
    }
}

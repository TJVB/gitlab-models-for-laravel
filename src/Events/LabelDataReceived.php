<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Label;

final class LabelDataReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;


    public function __construct(public Label $label)
    {
    }

    public function getLabel(): Label
    {
        return $this->label;
    }
}

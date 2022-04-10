<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Tag;

final class TagDataReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;


    public function __construct(public Tag $tag)
    {
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }
}

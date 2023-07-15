<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use TJVB\GitlabModelsForLaravel\Contracts\Models\User;

final class UserDataReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}

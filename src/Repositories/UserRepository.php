<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Repositories\UserWriteRepository;

final class UserRepository implements UserWriteRepository
{
    public function updateOrCreate(int $userId, array $userdata): ?int
    {
        // TODO: Implement updateOrCreate() method.
    }
}

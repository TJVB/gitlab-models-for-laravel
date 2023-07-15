<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\User;

interface UserWriteRepository
{
    public function updateOrCreate(int $userId, array $userdata): User;
}

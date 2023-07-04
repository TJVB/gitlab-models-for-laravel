<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Repositories;

interface UserWriteRepository
{
    public function updateOrCreate(int $userId, array $userdata): ?int;
}

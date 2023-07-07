<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Services;

interface UserUpdateServiceContract
{
    public function updateOrCreate(array $userData): void;
}

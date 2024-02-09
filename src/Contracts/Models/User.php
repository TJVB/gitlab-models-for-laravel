<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Models;

interface User
{
    public function getUserId(): int;

    public function getName(): string;

    public function getUsername(): string;

    public function getAvatarUrl(): string;
}

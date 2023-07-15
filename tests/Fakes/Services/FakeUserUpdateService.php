<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\UserUpdateServiceContract;

final class FakeUserUpdateService implements UserUpdateServiceContract
{
    public array $receivedData = [];

    public function updateOrCreate(array $userData): void
    {
        $this->receivedData[] = $userData;
    }
}

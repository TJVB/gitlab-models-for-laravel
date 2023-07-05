<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\UserWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\User;

final class UserRepository implements UserWriteRepository
{
    public function updateOrCreate(int $userId, array $userdata): User
    {
        $values = [];
        if (count($userdata) > 1) {
            $values =  [
                'name' => (string) Arr::get($userdata, 'name'),
                'username' => (string) Arr::get($userdata, 'username'),
                'avatar_url' => (string) Arr::get($userdata, 'avatar_url'),
            ];
        }
        return User::updateOrCreate(['user_id' => $userId], $values);
    }
}

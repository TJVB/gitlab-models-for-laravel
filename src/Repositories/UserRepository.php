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
        $keys = [
            'name',
            'username',
            'avatar_url',
        ];
        $values = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $userdata)) {
                $values[$key] = $userdata[$key];
            }
        }
        return User::updateOrCreate(['user_id' => $userId], $values);
    }
}

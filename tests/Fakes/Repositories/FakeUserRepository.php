<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories;

use TJVB\GitlabModelsForLaravel\Contracts\Models\User;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\UserWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\User as UserModel;

final class FakeUserRepository implements UserWriteRepository
{
    public ?User $result = null;
    public array $receivedData = [];
    public function updateOrCreate(int $userId, array $userdata): User
    {
        $this->receivedData[] = [
            'userId' => $userId,
            'userData' => $userdata,
        ];
        if ($this->result === null) {
            $this->result = new UserModel();
            $this->result->user_id = $userId;
            $this->result->name = 'name' . random_int(1, 100);
            $this->result->username = 'name' . random_int(1, 100);
            $this->result->avatar_url = 'https://example.com/' . random_int(1, 100);
        }
        return $this->result;
    }

    public function hasReceivedData(int $userId, array $userdata): bool
    {
        $search = [
            'userId' => $userId,
            'userData' => $userdata,
        ];
        return in_array($search, $this->receivedData, true);
    }
}

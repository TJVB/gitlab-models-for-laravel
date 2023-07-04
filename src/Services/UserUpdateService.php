<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\UserWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\UserUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\UserDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;

final class UserUpdateService implements UserUpdateServiceContract
{
    public function __construct(
        private readonly Repository $config,
        private readonly UserWriteRepository $userWriteRepository,
    ) {
    }

    public function updateOrCreate(array $userData): void
    {
        if (!$this->config->get('gitlab-models.model_to_store.projects')) {
            return;
        }
        if (!isset($userData['id'])) {
            throw MissingData::missingDataForAction('id', ' updateOrCreateUser');
        }
        $user = $this->userWriteRepository->updateOrCreate($userData['id'], $userData);

        UserDataReceived::dispatch($user);
    }
}

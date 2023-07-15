<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\UserWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\User;
use TJVB\GitlabModelsForLaravel\Repositories\UserRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

final class UserRepositoryTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $repository = new UserRepository();

        // verify/assert
        $this->assertInstanceOf(UserWriteRepository::class, $repository);
    }

    /**
     * @test
     */
    public function weCanCreateAnUser(): void
    {
        // setup / mock
        $userId = random_int(1, PHP_INT_MAX);
        $data = [
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'avatar_url' => $this->faker->imageUrl(),
        ];

        // run
        $repository = new UserRepository();
        $repository->updateOrCreate($userId, $data);

        // verify/assert
        $validationData = $data;
        $validationData['user_id'] = $userId;
        $this->assertDatabaseHas('gitlab_users', $validationData);
    }

    /**
     * @test
     */
    public function weCanCreateAnUserWithOnlyAnId(): void
    {
        // setup / mock
        $userId = random_int(1, PHP_INT_MAX);

        // run
        $repository = new UserRepository();
        $repository->updateOrCreate($userId, []);

        // verify/assert
        $this->assertDatabaseHas('gitlab_users', ['user_id' => $userId]);
    }

    /**
     * @test
     */
    public function weCanUpdateAnUser(): void
    {
        // setup / mock
        $userId = random_int(1, PHP_INT_MAX);
        User::create(['user_id' => $userId]);
        $data = [
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'avatar_url' => $this->faker->imageUrl(),
        ];

        // run
        $repository = new UserRepository();
        $repository->updateOrCreate($userId, $data);

        // verify/assert
        $validationData = $data;
        $validationData['user_id'] = $userId;
        $this->assertDatabaseHas('gitlab_users', $validationData);
    }

    /**
     * @test
     */
    public function weCanUpdateAnUserWithOnlyAnId(): void
    {
        // setup / mock
        $userId = random_int(1, PHP_INT_MAX);
        $data = [
            'user_id' => $userId,
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'avatar_url' => $this->faker->imageUrl(),
        ];
        User::create($data);

        // run
        $repository = new UserRepository();
        $repository->updateOrCreate($userId, []);

        // verify/assert
        $this->assertDatabaseHas('gitlab_users', $data);
    }
}

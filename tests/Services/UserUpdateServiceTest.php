<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\UserWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\UserUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\UserDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\UserUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeUserRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitlabModelsForLaravel\Tests\TrueFalseProvider;

final class UserUpdateServiceTest extends TestCase
{
    use TrueFalseProvider;
    use WithFaker;

    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $service = $this->app->make(UserUpdateService::class);

        // verify/assert
        $this->assertInstanceOf(UserUpdateServiceContract::class, $service);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weUseTheRepositoryToUpdateOrCreateAUser(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeUserRepository();
        $this->app->bind(UserWriteRepository::class, static function () use ($fakeRepository): UserWriteRepository {
            return $fakeRepository;
        });
        $userId = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $userId,
            'name' => $this->faker->name(),
            'username' => $this->faker->word(),
            'avatar_url' => $this->faker->url(),
        ];

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.users', $enabled);

        // run
        $service = $this->app->make(UserUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedData);
            $this->assertTrue(
                $fakeRepository->hasReceivedData($userId, $data),
                'We didn\'t received the correct data on the repository'
            );
            Event::assertDispatched(static function (UserDataReceived $event) use ($userId) {
                return $event->getUser()->getUserId() === $userId;
            });
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        Event::assertNotDispatched(UserDataReceived::class);
    }

    /**
     * @test
     * @dataProvider neededUserDataProvider
     */
    public function weGenerateAnErrorIfWeUpdateOrCreateATagWithoutTheNeededData(array $data): void
    {
        // setup / mock
        $service = $this->app->make(UserUpdateService::class);
        $this->expectException(MissingData::class);

        // run
        $service->updateOrCreate($data);
    }

    public function neededUserDataProvider(): array
    {
        return [
            'no data' => [[]],
            'no user_id' => [[
                'name' => 'the name',
                'username' => 'username123',
                'avatar_url' => 'https://example.example/avatar.extension',
            ]
            ],
        ];
    }
}

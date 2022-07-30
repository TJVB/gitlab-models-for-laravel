<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Event;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\BuildWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\BuildUpdateServiceContract as BuildUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;
use TJVB\GitlabModelsForLaravel\Events\BuildDataReceived;
use TJVB\GitlabModelsForLaravel\Services\BuildUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeBuildWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitlabModelsForLaravel\Tests\TrueFalseProvider;

class BuildUpdateServiceTest extends TestCase
{
    use TrueFalseProvider;

    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $service = $this->app->make(BuildUpdateService::class);

        // verify/assert
        $this->assertInstanceOf(BuildUpdateServiceContract::class, $service);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weUseTheRepositoryToUpdateOrCreateABuild(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeBuildWriteRepository();
        $this->app->bind(BuildWriteRepository::class, static function () use ($fakeRepository): BuildWriteRepository {
            return $fakeRepository;
        });
        $id = random_int(1, PHP_INT_MAX);
        $dto = BuildDTO::fromBuildEventData([
            'build_id' => $id,
            'pipeline_id' => random_int(1, PHP_INT_MAX),
            'project_id' => random_int(1, PHP_INT_MAX),
            'build_name' => md5((string)random_int(1, PHP_INT_MAX)),
            'build_stage' => md5((string)random_int(1, PHP_INT_MAX)),
            'build_status' => md5((string)random_int(1, PHP_INT_MAX)),
            'build_allow_failure' => (bool) random_int(0, 1),
            'build_created_at' => CarbonImmutable::now()->toIso8601ZuluString(),
        ]);

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.builds', $enabled);

        // run
        $service = $this->app->make(BuildUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($dto);

        // verify/assert
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedData);
            $this->assertTrue(
                $fakeRepository->hasReceivedData($id, $dto),
                'We didn\'t received the correct data on the repository'
            );
            Event::assertDispatched(static function (BuildDataReceived $event) use ($id) {
                return $event->getBuild()->getBuildId() === $id;
            });
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        $this->assertFalse(
            $fakeRepository->hasReceivedData($id, $dto),
            'We did received the data on the repository while disabled'
        );
        Event::assertNotDispatched(BuildDataReceived::class);
    }
}

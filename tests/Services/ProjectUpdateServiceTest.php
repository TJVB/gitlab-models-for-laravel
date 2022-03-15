<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService as ProjectUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\ProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class ProjectUpdateServiceTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $service = $this->app->make(ProjectUpdateService::class);

        // verify/assert
        $this->assertInstanceOf(ProjectUpdateServiceContract::class, $service);
    }
    /**
     * @test
     */
    public function weUseTheRepositoryToUpdateOrCreateAProject(): void
    {
        // setup / mock
        $fakeRepository = new FakeProjectWriteRepository();
        $this->app->bind(
            ProjectWriteRepository::class,
            static function () use ($fakeRepository): ProjectWriteRepository {

                return $fakeRepository;
            }
        );
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
        ];

        // run
        $service = $this->app->make(ProjectUpdateService::class);
        $service->updateOrCreate($data);

        // verify/assert
        $this->assertNotEmpty($fakeRepository->receivedData);
        $this->assertTrue(
            $fakeRepository->hasReceivedData($id, $data),
            'We didn\'t received the correct data on the repository'
        );
    }

    /**
     * @test
     */
    public function weGenerateAnErrorIfWeUpdateOrCreateAProjectWithoutID(): void
    {
        // setup / mock
        $service = $this->app->make(ProjectUpdateService::class);
        $this->expectException(MissingData::class);

        // run
        $service->updateOrCreate([]);
    }
}

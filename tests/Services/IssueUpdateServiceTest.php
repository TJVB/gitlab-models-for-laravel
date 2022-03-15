<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateService as IssueUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeIssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class IssueUpdateServiceTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $service = $this->app->make(IssueUpdateService::class);

        // verify/assert
        $this->assertInstanceOf(IssueUpdateServiceContract::class, $service);
    }
    /**
     * @test
     */
    public function weUseTheRepositoryToUpdateOrCreateAProject(): void
    {
        // setup / mock
        $fakeRepository = new FakeIssueWriteRepository();
        $this->app->bind(IssueWriteRepository::class, static function () use ($fakeRepository): IssueWriteRepository {

            return $fakeRepository;
        });
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
        ];

        // run
        $service = $this->app->make(IssueUpdateService::class);
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
        $service = $this->app->make(IssueUpdateService::class);
        $this->expectException(MissingData::class);

        // run
        $service->updateOrCreate([]);
    }
}

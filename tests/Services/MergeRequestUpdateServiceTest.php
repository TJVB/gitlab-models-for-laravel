<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Event;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\MergeRequestDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\MergeRequestUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeMergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitlabModelsForLaravel\Tests\TrueFalseProvider;

class MergeRequestUpdateServiceTest extends TestCase
{
    use TrueFalseProvider;

    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $service = $this->app->make(MergeRequestUpdateService::class);

        // verify/assert
        $this->assertInstanceOf(MergeRequestUpdateService::class, $service);
        $this->assertInstanceOf(MergeRequestUpdateServiceContract::class, $service);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weUseTheRepositoryToUpdateOrCreateAMergeRequest(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeMergeRequestWriteRepository();
        $this->app->bind(
            MergeRequestWriteRepository::class,
            static function () use ($fakeRepository): MergeRequestWriteRepository {
                return $fakeRepository;
            }
        );
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.merge_requests', $enabled);

        // run
        $service = $this->app->make(MergeRequestUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedData);
            $this->assertTrue(
                $fakeRepository->hasReceivedData($id, $data),
                'We didn\'t received the correct data on the repository'
            );
            Event::assertDispatched(static function (MergeRequestDataReceived $event) use ($id) {
                return $event->getMergeRequest()->getMergeRequestId() === $id;
            });
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        $this->assertFalse(
            $fakeRepository->hasReceivedData($id, $data),
            'We did received the data on the repository while disabled'
        );
        Event::assertNotDispatched(MergeRequestDataReceived::class);
    }

    /**
     * @test
     */
    public function weGenerateAnErrorIfWeUpdateOrCreateAMergeRequestWithoutID(): void
    {
        // setup / mock
        $service = $this->app->make(MergeRequestUpdateServiceContract::class);
        $this->expectException(MissingData::class);

        // run
        $service->updateOrCreate([]);
    }
}

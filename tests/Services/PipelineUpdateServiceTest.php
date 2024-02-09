<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Event;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\PipelineWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\PipelineUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\PipelineDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\PipelineUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakePipelineWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitlabModelsForLaravel\Tests\TrueFalseProvider;

final class PipelineUpdateServiceTest extends TestCase
{
    use TrueFalseProvider;

    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $service = $this->app->make(PipelineUpdateService::class);

        // verify/assert
        $this->assertInstanceOf(PipelineUpdateServiceContract::class, $service);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weUseTheRepositoryToUpdateOrCreateAMergeRequest(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakePipelineWriteRepository();
        $this->app->bind(
            PipelineWriteRepository::class,
            static function () use ($fakeRepository): PipelineWriteRepository {
                return $fakeRepository;
            }
        );
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
        ];

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.pipelines', $enabled);

        // run
        $service = $this->app->make(PipelineUpdateService::class, [
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
            Event::assertDispatched(static function (PipelineDataReceived $event) use ($id) {
                return $event->getPipeline()->getPipelineId() === $id;
            });
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        $this->assertFalse(
            $fakeRepository->hasReceivedData($id, $data),
            'We did received the data on the repository while disabled'
        );
        Event::assertNotDispatched(PipelineDataReceived::class);
    }

    /**
     * @test
     */
    public function weGenerateAnErrorIfWeUpdateOrCreateAMergeRequestWithoutID(): void
    {
        // setup / mock
        $service = $this->app->make(PipelineUpdateService::class);
        $this->expectException(MissingData::class);

        // run
        $service->updateOrCreate([]);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Event;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\LabelWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\LabelUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\LabelDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\LabelUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeLabelWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitlabModelsForLaravel\Tests\TrueFalseProvider;

final class LabelUpdateServiceTest extends TestCase
{
    use TrueFalseProvider;

    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $service = $this->app->make(LabelUpdateService::class);

        // verify/assert
        $this->assertInstanceOf(LabelUpdateServiceContract::class, $service);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weUseTheRepositoryToUpdateOrCreateALabel(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeLabelWriteRepository();
        $this->app->bind(LabelWriteRepository::class, static function () use ($fakeRepository): LabelWriteRepository {

            return $fakeRepository;
        });
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'title' => 'the title',
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.labels', $enabled);

        // run
        /** @var LabelUpdateService $service */
        $service = $this->app->make(LabelUpdateService::class, [
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
            Event::assertDispatched(static function (LabelDataReceived $event) use ($id) {
                return $event->getLabel()->getLabelId() === $id;
            });
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        $this->assertFalse(
            $fakeRepository->hasReceivedData($id, $data),
            'We did received the data on the repository while disabled'
        );
        Event::assertNotDispatched(LabelDataReceived::class);
    }

    /**
     * @test
     */
    public function weGenerateAnErrorIfWeUpdateOrCreateAnIssueWithoutID(): void
    {
        // setup / mock
        $service = $this->app->make(LabelUpdateService::class);
        $this->expectException(MissingData::class);

        // run
        $service->updateOrCreate([]);
    }
}

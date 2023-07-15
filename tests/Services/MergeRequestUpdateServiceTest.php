<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Event;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\LabelUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\MergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\DTOs\LabelDTO;
use TJVB\GitlabModelsForLaravel\Events\MergeRequestDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\MergeRequestUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeMergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeLabelUpdateService;
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

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weCanStoreTheLabelsIfProvided(bool $enabled): void
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
        $fakeLabelUpdateService = new FakeLabelUpdateService();
        $this->app->bind(
            LabelUpdateServiceContract::class,
            static function () use ($fakeLabelUpdateService): LabelUpdateServiceContract {
                return $fakeLabelUpdateService;
            }
        );
        $fakeLabelUpdateService->result = new LabelDTO(
            random_int(1, PHP_INT_MAX),
            md5((string)mt_rand()),
            md5((string)mt_rand()),
            random_int(1, PHP_INT_MAX),
            CarbonImmutable::now()->subMinutes(random_int(10, 20)),
            CarbonImmutable::now(),
            md5((string)mt_rand()),
            md5((string)mt_rand()),
            random_int(1, PHP_INT_MAX),
        );
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
            'labels' => [
                [
                    'title' => $fakeLabelUpdateService->result->title
                ],
            ],
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.merge_requests', true);
        $config->set('gitlab-models.merge_request_relations.labels', $enabled);

        // run
        $service = $this->app->make(MergeRequestUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        $this->assertNotEmpty($fakeRepository->receivedData);
        $this->assertTrue(
            $fakeRepository->hasReceivedData($id, $data),
            'We didn\'t received the correct data on the repository'
        );
        Event::assertDispatched(static function (MergeRequestDataReceived $event) use ($id) {
            return $event->getMergeRequest()->getMergeRequestId() === $id;
        });
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedSync);
            return;
        }
        $this->assertEmpty($fakeRepository->receivedSync);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weSyncTheAssignee(bool $enabled): void
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
        $assigneeId = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
            'assignee_id' => $assigneeId,
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.merge_requests', true);
        $config->set('gitlab-models.merge_request_relations.assignees', $enabled);

        // run
        $service = $this->app->make(MergeRequestUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        $this->assertNotEmpty($fakeRepository->receivedData);
        $this->assertTrue(
            $fakeRepository->hasReceivedData($id, $data),
            'We didn\'t received the correct data on the repository'
        );
        Event::assertDispatched(static function (MergeRequestDataReceived $event) use ($id) {
            return $event->getMergeRequest()->getMergeRequestId() === $id;
        });
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedAssignees);
            $this->assertTrue($fakeRepository->hasReceivedAssignees($id, [$assigneeId]));
            return;
        }
        $this->assertEmpty($fakeRepository->receivedAssignees);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weSyncTheAssignees(bool $enabled): void
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
        $assigneeId1 = random_int(1, PHP_INT_MAX);
        $assigneeId2 = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
            'assignee_ids' => [
                $assigneeId1,
                $assigneeId2,
            ],
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.merge_requests', true);
        $config->set('gitlab-models.merge_request_relations.assignees', $enabled);

        // run
        $service = $this->app->make(MergeRequestUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        $this->assertNotEmpty($fakeRepository->receivedData);
        $this->assertTrue(
            $fakeRepository->hasReceivedData($id, $data),
            'We didn\'t received the correct data on the repository'
        );
        Event::assertDispatched(static function (MergeRequestDataReceived $event) use ($id) {
            return $event->getMergeRequest()->getMergeRequestId() === $id;
        });
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedAssignees);
            $this->assertTrue($fakeRepository->hasReceivedAssignees($id, [$assigneeId1, $assigneeId2]));
            return;
        }
        $this->assertEmpty($fakeRepository->receivedAssignees);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weSyncTheReviewers(bool $enabled): void
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
        $reviewerId = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
            'reviewer_ids' => [
                $reviewerId,
            ],
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.merge_requests', true);
        $config->set('gitlab-models.merge_request_relations.reviewers', $enabled);

        // run
        $service = $this->app->make(MergeRequestUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        $this->assertNotEmpty($fakeRepository->receivedData);
        $this->assertTrue(
            $fakeRepository->hasReceivedData($id, $data),
            'We didn\'t received the correct data on the repository'
        );
        Event::assertDispatched(static function (MergeRequestDataReceived $event) use ($id) {
            return $event->getMergeRequest()->getMergeRequestId() === $id;
        });
        if ($enabled) {
            $this->assertTrue($fakeRepository->hasReceivedReviewers($id, [$reviewerId]));
            return;
        }
        $this->assertEmpty($fakeRepository->receivedReviewers);
    }
}

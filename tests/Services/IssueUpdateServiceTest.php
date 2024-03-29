<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Event;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\IssueUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Contracts\Services\LabelUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\DTOs\LabelDTO;
use TJVB\GitlabModelsForLaravel\Events\IssueDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeIssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeLabelUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitlabModelsForLaravel\Tests\TrueFalseProvider;

final class IssueUpdateServiceTest extends TestCase
{
    use TrueFalseProvider;

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
     * @dataProvider trueFalseProvider
     */
    public function weUseTheRepositoryToUpdateOrCreateAnIssue(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeIssueWriteRepository();
        $this->app->bind(IssueWriteRepository::class, static function () use ($fakeRepository): IssueWriteRepository {
            return $fakeRepository;
        });
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
        ];

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.issues', $enabled);

        // run
        $service = $this->app->make(IssueUpdateService::class, [
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
            Event::assertDispatched(static function (IssueDataReceived $event) use ($id) {
                return $event->getIssue()->getIssueId() === $id;
            });
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        $this->assertFalse(
            $fakeRepository->hasReceivedData($id, $data),
            'We did received the data on the repository while disabled'
        );
        Event::assertNotDispatched(IssueDataReceived::class);
    }

    /**
     * @test
     */
    public function weGenerateAnErrorIfWeUpdateOrCreateAnIssueWithoutID(): void
    {
        // setup / mock
        $service = $this->app->make(IssueUpdateService::class);
        $this->expectException(MissingData::class);

        // run
        $service->updateOrCreate([]);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weStoreTheLabelsIfProvided(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeIssueWriteRepository();
        $this->app->bind(IssueWriteRepository::class, static function () use ($fakeRepository): IssueWriteRepository {
            return $fakeRepository;
        });
        $fakeLabelUpdateService = new FakeLabelUpdateService();
        $this->app->bind(
            LabelUpdateServiceContract::class,
            static function () use ($fakeLabelUpdateService): LabelUpdateServiceContract {
                return $fakeLabelUpdateService;
            }
        );
        $fakeLabelUpdateService->result = new LabelDTO(
            random_int(1, PHP_INT_MAX),
            md5((string) mt_rand()),
            md5((string) mt_rand()),
            random_int(1, PHP_INT_MAX),
            CarbonImmutable::now()->subMinutes(random_int(10, 20)),
            CarbonImmutable::now(),
            md5((string) mt_rand()),
            md5((string) mt_rand()),
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

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.issues', true);
        $config->set('gitlab-models.issue_relations.labels', $enabled);

        // run
        $service = $this->app->make(IssueUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        $this->assertNotEmpty($fakeRepository->receivedData);
        $this->assertTrue(
            $fakeRepository->hasReceivedData($id, $data),
            'We didn\'t received the correct data on the repository'
        );
        Event::assertDispatched(static function (IssueDataReceived $event) use ($id) {
            return $event->getIssue()->getIssueId() === $id;
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
    public function weStoreTheAllAssigneesIfEnabled(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeIssueWriteRepository();
        $this->app->bind(IssueWriteRepository::class, static function () use ($fakeRepository): IssueWriteRepository {
            return $fakeRepository;
        });
        $assigneeId1 = random_int(1, PHP_INT_MAX);
        $assigneeId2 = random_int(1, PHP_INT_MAX);
        $assigneeId3 = random_int(1, PHP_INT_MAX);
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
            'assignee_ids' => [
                $assigneeId1,
                $assigneeId2,
            ],
            'assignee_id' => $assigneeId3,
        ];

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.issues', true);
        $config->set('gitlab-models.issue_relations.assignees', $enabled);

        // run
        $service = $this->app->make(IssueUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedSyncAssignees);
            $this->assertTrue(
                $fakeRepository->hasReceivedAssignees($id, [$assigneeId1, $assigneeId2, $assigneeId3]),
                'We didn\'t received the correct assignee ids on the repository'
            );
            return;
        }
        $this->assertEmpty($fakeRepository->receivedSyncAssignees);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weStoreTheAssigneeIfEnabled(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeIssueWriteRepository();
        $this->app->bind(IssueWriteRepository::class, static function () use ($fakeRepository): IssueWriteRepository {
            return $fakeRepository;
        });
        $assigneeId = random_int(1, PHP_INT_MAX);
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
            'assignee_id' => $assigneeId,
        ];

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.issues', true);
        $config->set('gitlab-models.issue_relations.assignees', $enabled);

        // run
        $service = $this->app->make(IssueUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedSyncAssignees);
            $this->assertTrue(
                $fakeRepository->hasReceivedAssignees($id, [$assigneeId]),
                'We didn\'t received the correct assignee ids on the repository'
            );
            return;
        }
        $this->assertEmpty($fakeRepository->receivedSyncAssignees);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weStoreTheAssigneesIfEnabled(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeIssueWriteRepository();
        $this->app->bind(IssueWriteRepository::class, static function () use ($fakeRepository): IssueWriteRepository {
            return $fakeRepository;
        });
        $assigneeId1 = random_int(1, PHP_INT_MAX);
        $assigneeId2 = random_int(1, PHP_INT_MAX);
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
            'assignee_ids' => [
                $assigneeId1,
                $assigneeId2,
            ],
        ];

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.issues', true);
        $config->set('gitlab-models.issue_relations.assignees', $enabled);

        // run
        $service = $this->app->make(IssueUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedSyncAssignees);
            $this->assertTrue(
                $fakeRepository->hasReceivedAssignees($id, [$assigneeId1, $assigneeId2]),
                'We didn\'t received the correct assignee ids on the repository'
            );
            return;
        }
        $this->assertEmpty($fakeRepository->receivedSyncAssignees);
    }
}

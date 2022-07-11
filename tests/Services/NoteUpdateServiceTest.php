<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Event;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\NoteWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteUpdateService as NoteUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\NoteDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\NoteUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeNoteWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitlabModelsForLaravel\Tests\TrueFalseProvider;

class NoteUpdateServiceTest extends TestCase
{
    use TrueFalseProvider;

/**
     * @test
     */


    public function weImplementTheContract(): void
    {
        // run
        $service = $this->app->make(NoteUpdateService::class);

        // verify/assert
        $this->assertInstanceOf(NoteUpdateServiceContract::class, $service);
    }

    /**
     * @test
     * @dataProvider shouldHandleProvider
     */
    public function weUseTheRepositoryToUpdateTheNoteAndDispatchAnEvent(
        bool $enabled,
        string $noteableType,
        array $noteableTypeConfig,
        bool $expected
    ): void {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeNoteWriteRepository();
        $this->app->bind(
            NoteWriteRepository::class,
            static function () use ($fakeRepository): NoteWriteRepository {
                return $fakeRepository;
            }
        );
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'id' => $id,
            'key' => 'value',
            'noteable_type' => $noteableType
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.notes', $enabled);
        $config->set('gitlab-models.comment_types_to_store', $noteableTypeConfig);

        // run
        $service = $this->app->make(NoteUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        if ($expected) {
            $this->assertNotEmpty($fakeRepository->receivedData);
            $this->assertTrue(
                $fakeRepository->hasReceivedData($id, $data),
                'We didn\'t received the correct data on the repository'
            );
            Event::assertDispatched(static function (NoteDataReceived $event) use ($id) {

                return $event->getNote()->getNoteId() === $id;
            });
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        $this->assertFalse(
            $fakeRepository->hasReceivedData($id, $data),
            'We did received the correct data on the repository while disabled'
        );
        Event::assertNotDispatched(NoteDataReceived::class);
    }

    public function shouldHandleProvider(): array
    {
        $options = [];
        $types = [
            'Commit',
            'MergeRequest',
            'Issue',
            'Snippet',
        ];
        foreach ([true, false] as $enabled) {
            foreach ($types as $type) {
                foreach ($types as $enabledType) {
                    $result = $enabled && $type === $enabledType;
                    $name = ($enabled ? 'enabled' : 'not enabled') . ' - ' .
                        $type . ' - ' . $enabledType . ' ' .
                        ($result ? 'should be handled' : 'should not be handled');
                    $options[$name] = [
                        $enabled,
                        $type,
                        [$enabledType],
                        $result
                    ];
                }
            }
        }
        return $options;
    }

    /**
     * @test
     */
    public function weGenerateAnErrorIfWeUpdateOrCreateAProjectWithoutID(): void
    {
        // setup / mock
        $service = $this->app->make(NoteUpdateService::class);
        $this->expectException(MissingData::class);

        // run
        $service->updateOrCreate([
            'noteable_type' => 'Issue',
        ]);
    }

    /**
     * @test
     */
    public function weGenerateAnErrorIfWeUpdateOrCreateAProjectWithoutANoteableType(): void
    {
        // setup / mock
        $service = $this->app->make(NoteUpdateService::class);
        $this->expectException(MissingData::class);

        // run
        $service->updateOrCreate([
            'id' => 123,
        ]);
    }

    /**
     * @test
     * @dataProvider noteIdProvider
     */
    public function weHandleTheDifferentIdValues(mixed $id, bool $valid): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeNoteWriteRepository();
        $this->app->bind(
            NoteWriteRepository::class,
            static function () use ($fakeRepository): NoteWriteRepository {
                return $fakeRepository;
            }
        );
        $data = [
            'id' => $id,
            'key' => 'value',
            'noteable_type' => 'Snippet'
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.notes', true);
        $config->set('gitlab-models.comment_types_to_store', ['Snippet']);

        if (!$valid) {
            $this->expectException(MissingData::class);
        }

        // run
        $service = $this->app->make(NoteUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        if ($valid) {
            $this->assertNotEmpty($fakeRepository->receivedData);
            $this->assertTrue(
                $fakeRepository->hasReceivedData((int)$id, $data),
                'We didn\'t received the correct data on the repository'
            );
            Event::assertDispatched(static function (NoteDataReceived $event) use ($id) {

                return $event->getNote()->getNoteId() === (int)$id;
            });
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        $this->assertFalse(
            $fakeRepository->hasReceivedData($id, $data),
            'We did received the correct data on the repository while disabled'
        );
        Event::assertNotDispatched(NoteDataReceived::class);
    }

    public function noteIdProvider(): array
    {
        return [
            'valid int' => [
                123,
                true,
            ],
            'valid string' => [
                '123',
                true,
            ],
            'invalid int' => [
                'n123',
                false,
            ],
        ];
    }
}

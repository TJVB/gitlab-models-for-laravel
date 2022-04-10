<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Event;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\ProjectUpdateService as ProjectUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\ProjectDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\ProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitlabModelsForLaravel\Tests\TrueFalseProvider;

class ProjectUpdateServiceTest extends TestCase
{
    use TrueFalseProvider;

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
     * @dataProvider trueFalseProvider
     */
    public function weUseTheRepositoryToUpdateOrCreateAProject(bool $enabled): void
    {
        // setup / mock
        Event::fake();
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

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.projects', $enabled);

        // run
        $service = $this->app->make(ProjectUpdateService::class, [
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
            Event::assertDispatched(static function (ProjectDataReceived $event) use ($id) {
                return $event->getProject()->getProjectId() === $id;
            });
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        $this->assertFalse(
            $fakeRepository->hasReceivedData($id, $data),
            'We did received the correct data on the repository while disabled'
        );
        Event::assertNotDispatched(ProjectDataReceived::class);
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

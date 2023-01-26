<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Event;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\DeploymentWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\DeploymentUpdateService as DeploymentUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Events\DeploymentDataReceived;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\DeploymentUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeDeploymentWritRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitlabModelsForLaravel\Tests\TrueFalseProvider;

class DeploymentUpdateServiceTest extends TestCase
{
    use TrueFalseProvider;


    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $config = $this->app->make(Repository::class);
        $repository = $this->app->make(DeploymentWriteRepository::class);
        $service = new DeploymentUpdateService($config, $repository);

        // verify/assert
        $this->assertInstanceOf(DeploymentUpdateServiceContract::class, $service);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weUseTheRepositoryToUpdateOrCreateADeployment(bool $enabled): void
    {
        // setup / mock
        Event::fake();
        $fakeRepository = new FakeDeploymentWritRepository();
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'deployment_id' => $id,
            'key' => 'value',
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.deployments', $enabled);

        // run
        $service = new DeploymentUpdateService($config, $fakeRepository);
        $service->updateOrCreate($data);

        // verify/assert
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedData);
            $this->assertTrue(
                $fakeRepository->hasReceivedData($id, $data),
                'We didn\'t received the correct data on the repository'
            );
            Event::assertDispatched(static function (DeploymentDataReceived $event) use ($id) {

                return $event->getDeployment()->getDeploymentId() === $id;
            });
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        $this->assertFalse(
            $fakeRepository->hasReceivedData($id, $data),
            'We did received the data on the repository while disabled'
        );
        Event::assertNotDispatched(DeploymentDataReceived::class);
    }

    /**
     * @test
     */
    public function weGenerateAnErrorIfWeUpdateOrCreateADeploymentWithoutID(): void
    {
        // setup / mock
        $config = $this->app->make(Repository::class);
        $repository = $this->app->make(DeploymentWriteRepository::class);

        // verify/assert
        $this->expectException(MissingData::class);

        // run
        $service = new DeploymentUpdateService($config, $repository);
        $service->updateOrCreate([]);
    }
}

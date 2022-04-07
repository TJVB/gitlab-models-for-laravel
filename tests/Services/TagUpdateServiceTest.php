<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use Illuminate\Contracts\Config\Repository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\TagWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateService as TagUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\TagUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeTagWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use TJVB\GitlabModelsForLaravel\Tests\TrueFalseProvider;

class TagUpdateServiceTest extends TestCase
{
    use TrueFalseProvider;

    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $service = $this->app->make(TagUpdateService::class);

        // verify/assert
        $this->assertInstanceOf(TagUpdateServiceContract::class, $service);
    }

    /**
     * @test
     * @dataProvider trueFalseProvider
     */
    public function weUseTheRepositoryToUpdateOrCreateATag(bool $enabled): void
    {
        // setup / mock
        $fakeRepository = new FakeTagWriteRepository();
        $this->app->bind(TagWriteRepository::class, static function () use ($fakeRepository): TagWriteRepository {

            return $fakeRepository;
        });

        $ref = 'refs/tags/v' . random_int(1, 10) . '.' . random_int(0, 100) . '.' . random_int(0, 100);
        $projectId = random_int(1, PHP_INT_MAX);
        $checkoutSha = sha1('random' . random_int(1, PHP_INT_MAX));
        $data = [
            'ref' => $ref,
            'project_id' => $projectId,
            'checkout_sha' => $checkoutSha,
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        $config->set('gitlab-models.model_to_store.tags', $enabled);

        // run
        $service = $this->app->make(TagUpdateService::class, [
            'config' => $config,
        ]);
        $service->updateOrCreate($data);

        // verify/assert
        if ($enabled) {
            $this->assertNotEmpty($fakeRepository->receivedData);
            $this->assertTrue(
                $fakeRepository->hasReceivedData($projectId, $ref, $data),
                'We didn\'t received the correct data on the repository'
            );
            return;
        }
        $this->assertEmpty($fakeRepository->receivedData);
        $this->assertFalse(
            $fakeRepository->hasReceivedData($projectId, $ref, $data),
            'We did received the correct data on the repository while disabled'
        );
    }

    /**
     * @test
     * @dataProvider neededTagDataProvider
     */
    public function weGenerateAnErrorIfWeUpdateOrCreateATagWithoutTheNeededData(array $data): void
    {
        // setup / mock
        $service = $this->app->make(TagUpdateService::class);
        $this->expectException(MissingData::class);

        // run
        $service->updateOrCreate($data);
    }

    public function neededTagDataProvider(): array
    {
        return [
            'no data' => [[]],
            'no project_id' => [['ref' => 'demoref']],
            'no ref' => [['project_id' => 12345]],
        ];
    }
}

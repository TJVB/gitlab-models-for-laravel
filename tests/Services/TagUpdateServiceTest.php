<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Repositories\TagWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Services\TagUpdateService as TagUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Exceptions\MissingData;
use TJVB\GitlabModelsForLaravel\Services\IssueUpdateService;
use TJVB\GitlabModelsForLaravel\Services\TagUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Repositories\FakeTagWriteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class TagUpdateServiceTest extends TestCase
{
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
     */
    public function weUseTheRepositoryToUpdateOrCreateATag(): void
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

        // run
        $service = $this->app->make(TagUpdateService::class);
        $service->updateOrCreate($data);

        // verify/assert
        $this->assertNotEmpty($fakeRepository->receivedData);
        $this->assertTrue(
            $fakeRepository->hasReceivedData($projectId, $ref, $data),
            'We didn\'t received the correct data on the repository'
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

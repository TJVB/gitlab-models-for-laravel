<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\PipelineWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Pipeline;
use TJVB\GitlabModelsForLaravel\Repositories\PipelineRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use function Safe\json_encode;

final class PipelineRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weImplementTheContracts(): void
    {
        // run
        $repository = new PipelineRepository();

        // verify/assert
        $this->assertInstanceOf(PipelineWriteRepository::class, $repository);
    }

    /**
     * @test
     */
    public function weCanCreateAPipeline(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'duration' => random_int(1, 500),
            'created_at' => CarbonImmutable::now()->subMinutes(3),
            'finished_at' => CarbonImmutable::now()->subMinutes(2),
            'ref' => md5((string) mt_rand()),
            'sha' => md5((string) mt_rand()),
            'source' => md5((string) mt_rand()),
            'stages' => [
                md5((string) mt_rand()),
                md5((string) mt_rand()),
            ],
            'status' => md5((string) mt_rand()),
            'tag' => random_int(0, 1),
            'project' => [
                'id' => random_int(1, PHP_INT_MAX)
            ],
        ];

        // run
        $repository = new PipelineRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $validationData = $data;
        $validationData['pipeline_id'] = $id;
        $validationData['project_id'] = $data['project']['id'];
        $validationData['stages'] = json_encode($data['stages']);
        unset($validationData['created_at'], $validationData['finished_at'], $validationData['project']);
        $this->assertEquals($id, $result->getPipelineId());
        $this->assertDatabaseHas('gitlab_pipelines', $validationData);
        $this->assertDatabaseCount('gitlab_pipelines', 1);

        $this->assertEquals($data['duration'], $result->getDuration());
        $this->assertEquals($data['created_at'], $result->getCreatedAt());
        $this->assertEquals($data['finished_at'], $result->getFinishedAt());
        $this->assertEquals($data['ref'], $result->getRef());
        $this->assertEquals($data['sha'], $result->getSha());
        $this->assertEquals($data['source'], $result->getSource());
        $this->assertEquals($data['stages'], $result->getStages());
        $this->assertEquals($data['status'], $result->getStatus());
        $this->assertEquals((bool) $data['tag'], $result->isTag());
        $this->assertEquals($data['project']['id'], $result->getProjectId());
    }

    /**
     * @test
     */
    public function weCanUpdateAPipeline(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'duration' => random_int(1, 500),
            'created_at' => CarbonImmutable::now()->subMinutes(3),
            'finished_at' => CarbonImmutable::now()->subMinutes(2),
            'ref' => md5((string) mt_rand()),
            'sha' => md5((string) mt_rand()),
            'source' => md5((string) mt_rand()),
            'stages' => [
                md5((string) mt_rand()),
                md5((string) mt_rand()),
            ],
            'status' => md5((string) mt_rand()),
            'tag' => random_int(0, 1),
            'project' => [
                'id' => random_int(1, PHP_INT_MAX),
            ],
        ];
        Pipeline::create([
            'pipeline_id' => $id,
            'duration' => random_int(1, 500),
            'pipeline_created_at' => CarbonImmutable::now()->subMinutes(3),
            'pipeline_finished_at' => CarbonImmutable::now()->subMinutes(2),
            'project_id' => random_int(1, PHP_INT_MAX),
            'ref' => md5((string) mt_rand()),
            'sha' => md5((string) mt_rand()),
            'source' => md5((string) mt_rand()),
            'stages' => [
                md5((string) mt_rand()),
                md5((string) mt_rand()),
            ],
            'status' => md5((string) mt_rand()),
            'tag' => random_int(0, 1),
        ]);

        // run
        $repository = new PipelineRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $validationData = $data;
        $validationData['pipeline_id'] = $id;
        $validationData['project_id'] = $validationData['project']['id'];
        $validationData['stages'] = json_encode($data['stages']);
        unset(
            $validationData['created_at'],
            $validationData['finished_at'],
            $validationData['project']
        );
        $this->assertEquals($id, $result->getPipelineId());
        $this->assertDatabaseHas('gitlab_pipelines', $validationData);
        $this->assertDatabaseCount('gitlab_pipelines', 1);
    }
}

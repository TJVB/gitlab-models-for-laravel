<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\BuildWriteRepository;
use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;
use TJVB\GitlabModelsForLaravel\Models\Build;
use TJVB\GitlabModelsForLaravel\Repositories\BuildRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

final class BuildRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weImplementTheContracts(): void
    {
        // run
        $repository = new BuildRepository();

        // verify/assert
        $this->assertInstanceOf(BuildWriteRepository::class, $repository);
    }

    /**
     * @test
     */
    public function weCanCreateABuild(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $pipelineId = random_int(1, PHP_INT_MAX);
        $projectId = random_int(1, PHP_INT_MAX);
        $name = 'job' . random_int(1, PHP_INT_MAX);
        $stage = 'stage' . mt_rand();
        $status = 'status' . mt_rand();
        $allowFailure = (bool) random_int(0, 1);
        $createdAt = CarbonImmutable::now()->subMinutes(random_int(10, 20));

        $dto = new BuildDTO(
            $id,
            $pipelineId,
            $projectId,
            $name,
            $stage,
            $status,
            $allowFailure,
            $createdAt
        );

        // run
        $repository = new BuildRepository();
        $result = $repository->updateOrCreate($id, $dto);

        // verify/assert
        $this->assertEquals($id, $result->getBuildId());
        $this->assertEquals($pipelineId, $result->getPipelineId());
        $this->assertEquals($projectId, $result->getProjectId());
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($stage, $result->getStage());
        $this->assertEquals($status, $result->getStatus());
        $this->assertEquals($createdAt, $result->getCreatedAt());
        $this->assertEquals($allowFailure, $result->getAllowFailure());
        $this->assertTrue($createdAt->equalTo($result->getCreatedAt()));

        $this->assertDatabaseHas('gitlab_builds', [
            'build_id' => $id,
            'pipeline_id' => $pipelineId,
            'project_id' => $projectId,
            'name' => $name,
            'stage' => $stage,
            'status' => $status,
        ]);
    }

    /**
     * @test
     */
    public function weCanCreateABuildWithADuration(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $pipelineId = random_int(1, PHP_INT_MAX);
        $projectId = random_int(1, PHP_INT_MAX);
        $name = 'job' . random_int(1, PHP_INT_MAX);
        $stage = 'stage' . mt_rand();
        $status = 'status' . mt_rand();
        $allowFailure = (bool) random_int(0, 1);
        $createdAt = CarbonImmutable::now()->subMinutes(random_int(20, 29));
        $startedAt = CarbonImmutable::now()->subMinutes(random_int(10, 19));
        $finishedAt = CarbonImmutable::now()->subMinutes(random_int(1, 9));
        $duration = random_int(1, 1000) + 0.123;

        $dto = new BuildDTO(
            $id,
            $pipelineId,
            $projectId,
            $name,
            $stage,
            $status,
            $allowFailure,
            $createdAt,
            $startedAt,
            $finishedAt,
            $duration
        );

        // run
        $repository = new BuildRepository();
        $result = $repository->updateOrCreate($id, $dto);

        // verify/assert
        $this->assertEquals($id, $result->getBuildId());
        $this->assertEquals($pipelineId, $result->getPipelineId());
        $this->assertEquals($projectId, $result->getProjectId());
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($stage, $result->getStage());
        $this->assertEquals($status, $result->getStatus());
        $this->assertEquals($createdAt, $result->getCreatedAt());
        $this->assertEquals($allowFailure, $result->getAllowFailure());
        $this->assertEquals($duration, $result->getDuration());
        $this->assertEquals($startedAt, $result->getStartedAt());
        $this->assertEquals($finishedAt, $result->getFinishedAt());

        $this->assertDatabaseHas('gitlab_builds', [
            'build_id' => $id,
            'pipeline_id' => $pipelineId,
            'project_id' => $projectId,
            'name' => $name,
            'stage' => $stage,
            'status' => $status,
            'duration' => $duration,
        ]);
    }

    /**
     * @test
     */
    public function weCanUpdateABuild(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $pipelineId = random_int(1, PHP_INT_MAX);
        $projectId = random_int(1, PHP_INT_MAX);
        $name = 'job' . random_int(1, PHP_INT_MAX);
        $stage = 'stage' . mt_rand();
        $status = 'status' . mt_rand();
        $allowFailure = (bool) random_int(0, 1);
        $createdAt = CarbonImmutable::now()->subMinute();
        $duration = random_int(1, 1000) + 0.123;

        $dto = new BuildDTO(
            $id,
            $pipelineId,
            $projectId,
            $name,
            $stage,
            $status,
            $allowFailure,
            $createdAt,
            null,
            null,
            $duration
        );
        Build::create([
            'build_id' => $id,
            'pipeline_id' => random_int(1, PHP_INT_MAX),
            'project_id' => random_int(1, PHP_INT_MAX),
            'name' => 'name' . random_int(1, PHP_INT_MAX),
            'stage' => 'stage' . random_int(1, PHP_INT_MAX),
            'status' => 'name' . random_int(1, PHP_INT_MAX),
            'build_created_at' => CarbonImmutable::now()->subHour(),
            'started_at' => null,
            'finished_at' => null,
            'allow_failure' => (bool) random_int(0, 1),
        ]);

        // run
        $repository = new BuildRepository();
        $result = $repository->updateOrCreate($id, $dto);

        // verify/assert
        $this->assertEquals($id, $result->getBuildId());
        $this->assertDatabaseHas('gitlab_builds', [
            'build_id' => $id,
            'pipeline_id' => $pipelineId,
            'project_id' => $projectId,
            'name' => $name,
            'stage' => $stage,
            'status' => $status,
            'duration' => $duration,
        ]);
        $this->assertDatabaseCount(Build::class, 1);
    }
}

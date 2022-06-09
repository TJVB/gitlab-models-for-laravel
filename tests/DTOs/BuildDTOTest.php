<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\DTOs;

use TJVB\GitlabModelsForLaravel\DTOs\BuildDTO;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

final class BuildDTOTest extends TestCase
{
    /**
     * @test
     */
    public function weCastTheIdValuesToIntInBuildEventData(): void
    {
        // setup / mock
        $id = random_int(1, 100000);
        $pipelineId = random_int(1, 100000);
        $projectId = random_int(1, 100000);
// run
        $dto = BuildDTO::fromBuildEventData([
           'build_id' => (string) $id,
           'pipeline_id' => (string) $pipelineId,
           'project_id' => (string) $projectId,
            'build_created_at' => '2022-06-09 22:37',
        ]);
// verify/assert
        $this->assertEquals($id, $dto->buildId);
        $this->assertEquals($pipelineId, $dto->pipelineId);
        $this->assertEquals($projectId, $dto->projectId);
    }
    /**
     * @test
     */
    public function weCastTheIdValuesToIntInPipelineEventData(): void
    {
        // setup / mock
        $id = random_int(1, 100000);
        $pipelineId = random_int(1, 100000);
        $projectId = random_int(1, 100000);
// run
        $dto = BuildDTO::fromPipelineEventData([
            'id' => (string) $id,
            'pipeline_id' => (string) $pipelineId,
            'project_id' => (string) $projectId,
            'created_at' => '2022-06-09 22:37',
        ]);
// verify/assert
        $this->assertEquals($id, $dto->buildId);
        $this->assertEquals($pipelineId, $dto->pipelineId);
        $this->assertEquals($projectId, $dto->projectId);
    }
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\LabelWriteRepository;
use TJVB\GitlabModelsForLaravel\Repositories\LabelRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

final class LabelRepositoryTest extends TestCase
{
    use DatabaseMigrations;

/**
     * @test
     */


    public function weImplementTheContract(): void
    {
        // run
        $repository = new LabelRepository();
    // verify/assert
        $this->assertInstanceOf(LabelWriteRepository::class, $repository);
    }

    /**
     * @test
     */
    public function weCanCreateALabel(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'label_id' => $id,
            'title' => md5((string)mt_rand()),
            'color' => md5((string)mt_rand()),
            'project_id' => random_int(1, PHP_INT_MAX),
            'created_at' => CarbonImmutable::now()->subMinutes(random_int(10, 20)),
            'updated_at' => CarbonImmutable::now(),
            'description' => md5((string)mt_rand()),
            'type' => md5((string)mt_rand()),
            'group_id' => random_int(1, PHP_INT_MAX),
        ];
// run
        $repository = new LabelRepository();
        $result = $repository->updateOrCreate($id, $data);
// verify/assert
        $this->assertEquals($id, $result->getLabelId());
        $this->assertEquals($data['title'], $result->getTitle());
        $this->assertEquals($data['color'], $result->getColor());
        $this->assertEquals($data['project_id'], $result->getProjectId());
        $this->assertEquals($data['description'], $result->getDescription());
        $this->assertEquals($data['type'], $result->getType());
        $this->assertEquals($data['group_id'], $result->getGroupId());
        $this->assertTrue($data['created_at']->equalTo($result->getCreatedAt()));
        $this->assertTrue($data['updated_at']->equalTo($result->getUpdatedAt()));
        unset($data['created_at'], $data['updated_at']);
        $this->assertDatabaseHas('gitlab_labels', $data);
    }
}

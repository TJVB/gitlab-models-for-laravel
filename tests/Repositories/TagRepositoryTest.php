<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\TagWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Tag;
use TJVB\GitlabModelsForLaravel\Repositories\TagRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class TagRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weImplementTheContracts(): void
    {
        // run
        $repository = new TagRepository();

        // verify/assert
        $this->assertInstanceOf(TagWriteRepository::class, $repository);
    }

    /**
     * @test
     */
    public function weCanCreateATag(): void
    {
        // setup / mock
        $ref = 'refs/tags/v' . random_int(1, 10) . '.' . random_int(0, 100) . '.' . random_int(0, 100);
        $projectId = random_int(1, PHP_INT_MAX);
        $checkoutSha = sha1('random' . random_int(1, PHP_INT_MAX));
        $data = [
            'ref' => $ref,
            'project_id' => $projectId,
            'checkout_sha' => $checkoutSha,
        ];

        // run
        $repository = new TagRepository();
        $result = $repository->updateOrCreate($projectId, $ref, $data);

        // verify/assert
        $validationData = $data;
        $this->assertEquals($ref, $result->getRef());
        $this->assertEquals($projectId, $result->getProjectId());
        $this->assertEquals($checkoutSha, $result->getCheckoutSha());
        $this->assertDatabaseHas('gitlab_tags', $validationData);
    }

    /**
     * @test
     */
    public function weCanCreateATagWithOnlyAProjectIDAndRef(): void
    {
        // setup / mock
        $ref = 'refs/tags/v' . random_int(1, 10) . '.' . random_int(0, 100) . '.' . random_int(0, 100);
        $projectId = random_int(1, PHP_INT_MAX);

        // run
        $repository = new TagRepository();
        $result = $repository->updateOrCreate($projectId, $ref, []);

        // verify/assert
        $this->assertEquals($projectId, $result->getProjectId());
        $this->assertEquals($ref, $result->getRef());
        $this->assertDatabaseHas('gitlab_tags', ['project_id' => $projectId, 'ref' => $ref]);
    }

    /**
     * @test
     */
    public function weCanUpdateATag(): void
    {
        // setup / mock
        $ref = 'refs/tags/v' . random_int(1, 10) . '.' . random_int(0, 100) . '.' . random_int(0, 100);
        $projectId = random_int(1, PHP_INT_MAX);
        $data = [
            'ref' => $ref,
            'project_id' => $projectId,
            'checkout_sha' => sha1('random' . random_int(1, PHP_INT_MAX)),
        ];

        Tag::create([
            'project_id' => $projectId,
            'ref' => $ref,
            'checkout_sha' => sha1('other random' . random_int(1, PHP_INT_MAX)),
        ]);

        // run
        $repository = new TagRepository();
        $result = $repository->updateOrCreate($projectId, $ref, $data);

        // verify/assert
        $validationData = $data;
        $this->assertEquals($projectId, $result->getProjectId());
        $this->assertEquals($ref, $result->getRef());
        $this->assertDatabaseHas('gitlab_tags', $validationData);
        $this->assertDatabaseCount(Tag::class, 1);
    }
}

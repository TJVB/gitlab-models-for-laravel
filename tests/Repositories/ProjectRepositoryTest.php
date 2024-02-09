<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectReadRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\ProjectWriteRepository;
use TJVB\GitlabModelsForLaravel\Exceptions\DataNotFound;
use TJVB\GitlabModelsForLaravel\Models\Project;
use TJVB\GitlabModelsForLaravel\Repositories\ProjectRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

final class ProjectRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weImplementTheContracts(): void
    {
        // run
        $repository = new ProjectRepository();

        // verify/assert
        $this->assertInstanceOf(ProjectWriteRepository::class, $repository);
        $this->assertInstanceOf(ProjectReadRepository::class, $repository);
    }

    /**
     * @test
     */
    public function weCanCreateAProject(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'name' => md5((string) mt_rand()),
            'web_url' => 'https://webtest' . mt_rand() . '.tld/web',
            'description' => md5((string) mt_rand()),
            'avatar_url' => 'https://test' . mt_rand() . '.tld/avatar',
            'visibility_level' => array_rand([
                0, // private
                10, // internal
                20, // public
            ]),
            'project_id' => $id,
        ];

        // run
        $repository = new ProjectRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $this->assertEquals($id, $result->getProjectId());
        $this->assertDatabaseHas('gitlab_projects', $data);
    }

    /**
     * @test
     */
    public function weCanCreateAProjectWithOnlyAnId(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);

        // run
        $repository = new ProjectRepository();
        $result = $repository->updateOrCreate($id, []);

        // verify/assert
        $this->assertEquals($id, $result->getProjectId());
        $this->assertDatabaseHas('gitlab_projects', ['project_id' => $id]);
    }

    /**
     * @test
     */
    public function weCanUpdateAProject(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'name' => md5((string) mt_rand()),
            'web_url' => 'https://webtest' . mt_rand() . '.tld/web',
            'description' => md5((string) mt_rand()),
            'avatar_url' => 'https://test' . mt_rand() . '.tld/avatar',
            'visibility_level' => array_rand([
                0, // private
                10, // internal
                20, // public
            ]),
            'project_id' => $id,
        ];
        Project::create([
            'project_id' => $id,
            'name' => md5((string) mt_rand()),
            'web_url' => 'https://webtest' . mt_rand() . '.tld/web',
            'description' => md5((string) mt_rand()),
            'avatar_url' => 'https://test' . mt_rand() . '.tld/avatar',
            'visibility_level' => array_rand([
                0, // private
                10, // internal
                20, // public
            ]),
        ]);

        // run
        $repository = new ProjectRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $this->assertEquals($id, $result->getProjectId());
        $this->assertDatabaseHas('gitlab_projects', $data);
        $this->assertDatabaseCount(Project::class, 1);
    }

    /**
     * @test
     */
    public function weCanFindAProject(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'project_id' => $id,
            'name' => md5((string) mt_rand()),
            'web_url' => 'https://webtest' . mt_rand() . '.tld/web',
            'description' => md5((string) mt_rand()),
            'avatar_url' => 'https://test' . mt_rand() . '.tld/avatar',
            'visibility_level' => array_rand([
                0, // private
                10, // internal
                20, // public
            ]),
        ];
        Project::create($data);

        // run
        $repository = new ProjectRepository();
        $result = $repository->find($id);

        // verify/assert
        $this->assertEquals($data['name'], $result->getProjectName());
        $this->assertEquals($data['web_url'], $result->getWebUrl());
        $this->assertEquals($data['description'], $result->getDescription());
        $this->assertEquals($data['avatar_url'], $result->getAvatarUrl());
        $this->assertEquals($data['visibility_level'], $result->getVisibilityLevel());
    }

    /**
     * @test
     */
    public function weGetTheExpectedExceptionIfWeCanNotFindAProject(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $this->expectException(DataNotFound::class);

        // run
        $repository = new ProjectRepository();
        $repository->find($id);
    }
}

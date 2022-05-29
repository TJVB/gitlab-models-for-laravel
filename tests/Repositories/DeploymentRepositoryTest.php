<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\DeploymentWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Deployment;
use TJVB\GitlabModelsForLaravel\Repositories\DeploymentRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class DeploymentRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weImplementTheContracts(): void
    {
        // run
        $repository = new DeploymentRepository();

        // verify/assert
        $this->assertInstanceOf(DeploymentWriteRepository::class, $repository);
    }

    /**
     * @test
     */
    public function weCanCreateADeployment(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $deployableId = random_int(1, PHP_INT_MAX);
        $deployableUrl = 'https://webtest' . mt_rand() . '.tld/web';
        $environment = 'environment' . mt_rand();
        $status = 'status' . mt_rand();
        $changedAt = CarbonImmutable::now()->subMinutes(random_int(10, 20));
        $data = [
            'deployment_id' => $id,
            'deployable_id' => $deployableId,
            'deployable_url' => $deployableUrl,
            'environment' => $environment,
            'status' => $status,
            'status_changed_at' => $changedAt,
        ];

        // run
        $repository = new DeploymentRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $this->assertEquals($id, $result->getDeploymentId());
        $this->assertEquals($deployableUrl, $result->getDeployableUrl());
        $this->assertEquals($environment, $result->getEnvironment());
        $this->assertEquals($status, $result->getStatus());
        $this->assertEquals($changedAt, $result->getStatusChangedAt());
        $this->assertDatabaseHas('gitlab_deployments', [
            'deployment_id' => $id,
            'deployable_id' => $deployableId,
            'deployable_url' => $deployableUrl,
            'environment' => $environment,
            'status' => $status,
        ]);
    }

    /**
     * @test
     */
    public function weCanUpdateADeployment(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $deployableId = random_int(1, PHP_INT_MAX);
        $deployableUrl = 'https://webtest' . mt_rand() . '.tld/web';
        $environment = 'environment' . mt_rand();
        $status = 'status' . mt_rand();
        $changedAt = CarbonImmutable::now()->subMinutes(random_int(10, 20));
        $data = [
            'deployment_id' => $id,
            'deployable_id' => $deployableId,
            'deployable_url' => $deployableUrl,
            'environment' => $environment,
            'status' => $status,
            'status_changed_at' => $changedAt,
        ];
        Deployment::create([
            'deployment_id' => $id,
            'deployable_id' => random_int(1, PHP_INT_MAX),
            'deployable_url' => 'https://webtest' . mt_rand() . '.tld/web',
            'environment' => 'environment' . mt_rand(),
            'status' => 'status' . mt_rand(),
            'status_changed_at' => CarbonImmutable::now()->subMinutes(random_int(10, 20)),
        ]);

        // run
        $repository = new DeploymentRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $this->assertEquals($id, $result->getDeploymentId());
        $this->assertDatabaseHas('gitlab_deployments', [
            'deployment_id' => $id,
            'deployable_id' => $deployableId,
            'deployable_url' => $deployableUrl,
            'environment' => $environment,
            'status' => $status,
        ]);
        $this->assertDatabaseCount(Deployment::class, 1);
    }
}
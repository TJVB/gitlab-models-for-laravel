<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\DTOs\LabelDTO;
use TJVB\GitlabModelsForLaravel\Models\Issue;
use TJVB\GitlabModelsForLaravel\Models\Label;
use TJVB\GitlabModelsForLaravel\Models\MergeRequest;
use TJVB\GitlabModelsForLaravel\Repositories\MergeRequestRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class MergeRequestRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weImplementTheContracts(): void
    {
        // run
        $repository = new MergeRequestRepository();

        // verify/assert
        $this->assertInstanceOf(MergeRequestWriteRepository::class, $repository);
    }

    /**
     * @test
     */
    public function weCanCreateAMergeRequest(): void
    {
        // setup / mock
        $authorId = random_int(1, PHP_INT_MAX);
        $blockingDiscussionsResolved = random_int(0, 1);
        $description = md5((string)mt_rand());
        $createdAt = CarbonImmutable::now()->subMinutes(random_int(10, 20));
        $id = random_int(1, PHP_INT_MAX);
        $iid = random_int(1, PHP_INT_MAX);
        $mergeStatus = md5((string)mt_rand());
        $state = md5((string)mt_rand());
        $sourceProjectId = random_int(1, PHP_INT_MAX);
        $sourceBranch = md5((string)mt_rand());
        $targetProjectId = random_int(1, PHP_INT_MAX);
        $targetBranch = md5((string)mt_rand());
        $title = 'title' . random_int(1, PHP_INT_MAX);
        $updatedAt = CarbonImmutable::now()->subMinutes(random_int(1, 9));
        $url = 'https://webtest' . mt_rand() . '.tld/url';
        $workInProgress = random_int(0, 1);

        $data = [
            'author_id' => $authorId,
            'blocking_discussions_resolved' => $blockingDiscussionsResolved,
            'description' => $description,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'iid' => $iid,
            'merge_status' => $mergeStatus,
            'state' => $state,
            'source_project_id' => $sourceProjectId,
            'source_branch' => $sourceBranch,
            'target_project_id' => $targetProjectId,
            'target_branch' => $targetBranch,
            'title' => $title,
            'url' => $url,
            'work_in_progress' => $workInProgress,
        ];

        // run
        $repository = new MergeRequestRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $this->assertEquals($authorId, $result->getAuthorId());
        $this->assertEquals($blockingDiscussionsResolved, $result->getBlockingDiscussionsResolved());
        $this->assertEquals($description, $result->getDescription());
        $this->assertEquals($id, $result->getMergeRequestId());
        $this->assertEquals($iid, $result->getMergeRequestIid());
        $this->assertEquals($mergeStatus, $result->getMergeStatus());
        $this->assertEquals($state, $result->getState());
        $this->assertEquals($sourceProjectId, $result->getSourceProjectId());
        $this->assertEquals($sourceBranch, $result->getSourceBranch());
        $this->assertEquals($targetProjectId, $result->getTargetProjectId());
        $this->assertEquals($targetBranch, $result->getTargetBranch());
        $this->assertEquals($title, $result->getTitle());
        $this->assertEquals($url, $result->getUrl());
        $this->assertEquals($workInProgress, $result->getWorkInProgress());
        $this->assertTrue($createdAt->equalTo($result->getCreatedAt()));
        $this->assertTrue($updatedAt->equalTo($result->getUpdatedAt()));

        $validationData = $data;
        $validationData['merge_request_id'] = $id;
        $validationData['merge_request_iid'] = $iid;
        unset($validationData['created_at'], $validationData['updated_at'], $validationData['iid']);
        $this->assertDatabaseHas('gitlab_merge_requests', $validationData);
    }

    /**
     * @test
     */
    public function weCanUpdateAMergeRequest(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'author_id' => random_int(1, PHP_INT_MAX),
            'blocking_discussions_resolved' => random_int(0, 1),
            'description' => md5((string)mt_rand()),
            'created_at' => CarbonImmutable::now()->subMinutes(3),
            'updated_at' => CarbonImmutable::now()->subMinutes(2),
            'iid' => random_int(1, PHP_INT_MAX),
            'merge_status' => md5((string)mt_rand()),
            'state' => md5((string)mt_rand()),
            'source_project_id' => random_int(0, 1),
            'source_branch' => md5((string)mt_rand()),
            'target_project_id' => random_int(0, 1),
            'target_branch' => md5((string)mt_rand()),
            'title' => 'title' . random_int(1, PHP_INT_MAX),
            'url' => 'https://webtest' . mt_rand() . '.tld/url',
            'work_in_progress' => random_int(0, 1),
        ];
        MergeRequest::create([
            'merge_request_id' => $id,
            'author_id' => random_int(1, PHP_INT_MAX),
            'blocking_discussions_resolved' => random_int(0, 1),
            'description' => md5((string)mt_rand()),
            'merge_request_created_at' => CarbonImmutable::now()->subMinutes(3),
            'merge_request_updated_at' => CarbonImmutable::now()->subMinutes(2),
            'merge_request_iid' => random_int(1, PHP_INT_MAX),
            'merge_status' => md5((string)mt_rand()),
            'state' => md5((string)mt_rand()),
            'source_project_id' => random_int(0, 1),
            'source_branch' => md5((string)mt_rand()),
            'target_project_id' => random_int(0, 1),
            'target_branch' => md5((string)mt_rand()),
            'title' => 'title' . random_int(1, PHP_INT_MAX),
            'url' => 'https://webtest' . mt_rand() . '.tld/url',
            'work_in_progress' => random_int(0, 1),
        ]);

        // run
        $repository = new MergeRequestRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $validationData = $data;
        $validationData['merge_request_id'] = $id;
        $validationData['merge_request_iid'] = $data['iid'];
        unset($validationData['created_at'], $validationData['updated_at'], $validationData['iid']);
        $this->assertEquals($id, $result->getMergeRequestId());
        $this->assertDatabaseHas('gitlab_merge_requests', $validationData);
        $this->assertDatabaseCount('gitlab_merge_requests', 1);
    }

    /**
     * @test
     */
    public function weCanSyncTheLabels(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $mergeRequest = MergeRequest::create([
            'merge_request_id' => $id,
            'author_id' => random_int(1, PHP_INT_MAX),
            'blocking_discussions_resolved' => random_int(0, 1),
            'description' => md5((string)mt_rand()),
            'merge_request_created_at' => CarbonImmutable::now()->subMinutes(3),
            'merge_request_updated_at' => CarbonImmutable::now()->subMinutes(2),
            'merge_request_iid' => random_int(1, PHP_INT_MAX),
            'merge_status' => md5((string)mt_rand()),
            'state' => md5((string)mt_rand()),
            'source_project_id' => random_int(0, 1),
            'source_branch' => md5((string)mt_rand()),
            'target_project_id' => random_int(0, 1),
            'target_branch' => md5((string)mt_rand()),
            'title' => 'title' . random_int(1, PHP_INT_MAX),
            'url' => 'https://webtest' . mt_rand() . '.tld/url',
            'work_in_progress' => random_int(0, 1),
        ]);

        $label = Label::create([
            'label_id' => random_int(1, PHP_INT_MAX),
            'title' => md5((string)mt_rand()),
            'color' => md5((string)mt_rand()),
            'project_id' => random_int(1, PHP_INT_MAX),
            'label_created_at' => CarbonImmutable::now()->subMinutes(random_int(10, 20)),
            'label_updated_at' => CarbonImmutable::now(),
            'description' => md5((string)mt_rand()),
            'type' => md5((string)mt_rand()),
            'group_id' => random_int(1, PHP_INT_MAX),
        ]);

        // run
        $repository = new MergeRequestRepository();
        $result = $repository->syncLabels($id, [LabelDTO::fromLabel($label)]);

        // verify/assert
        $this->assertNotNull($result);
        $this->assertDatabaseHas(
            'gitlab_label_gitlab_merge_request',
            [
                'label_id' => $label->id,
                'merge_request_id' => $mergeRequest->id,
            ]
        );
    }

    /**
     * @test
     */
    public function weDonNotCrashIfWeTryToSyncForAnIssueThatIsNotStored(): void
    {
        // setup / mock
        $label = Label::create([
            'label_id' => random_int(1, PHP_INT_MAX),
            'title' => md5((string)mt_rand()),
            'color' => md5((string)mt_rand()),
            'project_id' => random_int(1, PHP_INT_MAX),
            'label_created_at' => CarbonImmutable::now()->subMinutes(random_int(10, 20)),
            'label_updated_at' => CarbonImmutable::now(),
            'description' => md5((string)mt_rand()),
            'type' => md5((string)mt_rand()),
            'group_id' => random_int(1, PHP_INT_MAX),
        ]);

        // run
        $repository = new MergeRequestRepository();
        $result = $repository->syncLabels(random_int(1, PHP_INT_MAX), [LabelDTO::fromLabel($label)]);

        // verify/assert
        $this->assertNull($result);
        $this->assertDatabaseCount('gitlab_label_gitlab_merge_request', 0);
    }
}

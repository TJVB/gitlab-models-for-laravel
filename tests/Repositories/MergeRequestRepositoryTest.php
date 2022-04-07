<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\Issue;
use TJVB\GitlabModelsForLaravel\Repositories\IssueRepository;
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
        $createdAt = CarbonImmutable::now()->subMinute();
        $id = random_int(1, PHP_INT_MAX);
        $iid = random_int(1, PHP_INT_MAX);
        $mergeStatus = md5((string)mt_rand());
        $state = md5((string)mt_rand());
        $sourceProjectId = random_int(1, PHP_INT_MAX);
        $sourceBranch = md5((string)mt_rand());
        $targetProjectId = random_int(1, PHP_INT_MAX);
        $targetBranch = md5((string)mt_rand());
        $title = 'title' . random_int(1, PHP_INT_MAX);
        $url = 'https://webtest' . mt_rand() . '.tld/url';
        $workInProgress = random_int(0, 1);

        $data = [
            'author_id' => $authorId,
            'blocking_discussions_resolved' => $blockingDiscussionsResolved,
            'description' => $description,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
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
        $this->markTestIncomplete('TODO');
        // setup / mock

        // run

        // verify/assert
    }
}

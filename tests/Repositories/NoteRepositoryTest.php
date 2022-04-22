<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\MergeRequestWriteRepository;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\NoteWriteRepository;
use TJVB\GitlabModelsForLaravel\Models\MergeRequest;
use TJVB\GitlabModelsForLaravel\Repositories\MergeRequestRepository;
use TJVB\GitlabModelsForLaravel\Repositories\NoteRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class NoteRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weImplementTheContracts(): void
    {
        // run
        $repository = new NoteRepository();

        // verify/assert
        $this->assertInstanceOf(NoteWriteRepository::class, $repository);
    }

    /**
     * @test
     */
    public function weCanCreateANoteWithMinimalData(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);

        $data = [
            'author_id' => random_int(1, PHP_INT_MAX),
            'noteable_type' => md5((string)mt_rand()),
            'created_at' => CarbonImmutable::now()->subMinutes(random_int(10, 20)),
            'updated_at' => CarbonImmutable::now()->subMinutes(random_int(1, 9)),
            'note' => md5((string)mt_rand()),
            'url' => 'https://webtest' . mt_rand() . '.tld/url',
        ];

        // run
        $repository = new NoteRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $this->assertEquals($id, $result->getNoteId());
        $this->assertEquals($data['author_id'], $result->getAuthorId());
        $this->assertEquals($data['note'], $result->getNote());
        $this->assertEquals($data['url'], $result->getUrl());
        $this->assertEquals($data['noteable_type'], $result->getNoteableType());
        $this->assertNull($result->getCommitId());
        $this->assertNull($result->getLineCode());
        $this->assertNull($result->getNoteableId());
        $this->assertTrue($data['created_at']->equalTo($result->getCreatedAt()));
        $this->assertTrue($data['updated_at']->equalTo($result->getUpdatedAt()));

        $validationData = $data;
        $validationData['note_id'] = $id;
        unset($validationData['created_at'], $validationData['updated_at']);
        $this->assertDatabaseHas('gitlab_notes', $validationData);
    }

    /**
     * @test
     */
    public function weCanCreateANoteWithAllData(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);

        $data = [
            'author_id' => random_int(1, PHP_INT_MAX),
            'commit_id' => md5((string)mt_rand()),
            'line_code' => md5((string)mt_rand()),
            'noteable_type' => md5((string)mt_rand()),
            'created_at' => CarbonImmutable::now()->subMinutes(random_int(10, 20)),
            'updated_at' => CarbonImmutable::now()->subMinutes(random_int(1, 9)),
            'note' => md5((string)mt_rand()),
            'project_id' => random_int(1, PHP_INT_MAX),
            'url' => 'https://webtest' . mt_rand() . '.tld/url',
        ];

        // run
        $repository = new NoteRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $this->assertEquals($id, $result->getNoteId());
        $this->assertEquals($data['author_id'], $result->getAuthorId());
        $this->assertEquals($data['note'], $result->getNote());
        $this->assertEquals($data['url'], $result->getUrl());
        $this->assertEquals($data['commit_id'], $result->getCommitId());
        $this->assertEquals($data['line_code'], $result->getLineCode());
        $this->assertEquals($data['project_id'], $result->getProjectId());
        $this->assertTrue($data['created_at']->equalTo($result->getCreatedAt()));
        $this->assertTrue($data['updated_at']->equalTo($result->getUpdatedAt()));

        $validationData = $data;
        $validationData['note_id'] = $id;
        unset($validationData['created_at'], $validationData['updated_at']);
        $this->assertDatabaseHas('gitlab_notes', $validationData);
    }

    /**
     * @test
     */
    public function weCanUpdateANote(): void
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
}

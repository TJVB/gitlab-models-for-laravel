<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use TJVB\GitlabModelsForLaravel\Contracts\Repositories\IssueWriteRepository;
use TJVB\GitlabModelsForLaravel\DTOs\LabelDTO;
use TJVB\GitlabModelsForLaravel\Models\Issue;
use TJVB\GitlabModelsForLaravel\Models\Label;
use TJVB\GitlabModelsForLaravel\Repositories\IssueRepository;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;

class IssueRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function weImplementTheContracts(): void
    {
        // run
        $repository = new IssueRepository();

        // verify/assert
        $this->assertInstanceOf(IssueWriteRepository::class, $repository);
    }

    /**
     * @test
     */
    public function weCanCreateAnIssue(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $iid = random_int(1, PHP_INT_MAX);
        $projectId = random_int(1, PHP_INT_MAX);
        $title = 'title' . random_int(1, PHP_INT_MAX);
        $url = 'https://webtest' . mt_rand() . '.tld/url';
        $description = md5((string)mt_rand());
        $state = array_rand([
            'opened' => 'opened',
            'closed' => 'closed',
        ]);
        $confidential = random_int(0, 1);
        $data = [
            'issue_id' => $id,
            'iid' => $iid,
            'project_id' => $projectId,
            'title' => $title,
            'url' => $url,
            'description' => $description,
            'state' => $state,
            'confidential' => $confidential,
        ];

        // run
        $repository = new IssueRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $validationData = $data;
        $validationData['issue_iid'] = $validationData['iid'];
        unset($validationData['iid']);
        $this->assertEquals($id, $result->getIssueId());
        $this->assertEquals($iid, $result->getIssueIid());
        $this->assertEquals($projectId, $result->getProjectId());
        $this->assertEquals($title, $result->getTitle());
        $this->assertEquals($url, $result->getUrl());
        $this->assertEquals($description, $result->getDescription());
        $this->assertEquals($state, $result->getState());
        $this->assertEquals((bool) $confidential, $result->getConfidential());

        $this->assertDatabaseHas('gitlab_issues', $validationData);
    }

    /**
     * @test
     */
    public function weCanCreateAnIssueWithOnlyAnID(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);

        // run
        $repository = new IssueRepository();
        $result = $repository->updateOrCreate($id, []);

        // verify/assert
        $this->assertEquals($id, $result->getIssueId());
        $this->assertDatabaseHas('gitlab_issues', [
            'issue_id' => $id,
            'issue_iid' => '',
            'project_id' => '',
            'title' => '',
            'description' => '',
            'url' => '',
            'state' => '',
            'confidential' => 0,
        ]);
    }

    /**
     * @test
     */
    public function weCanUpdateAnIssue(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $data = [
            'issue_id' => $id,
            'iid' => random_int(1, PHP_INT_MAX),
            'project_id' => random_int(1, PHP_INT_MAX),
            'title' => 'title' . random_int(1, PHP_INT_MAX),
            'url' => 'https://webtest' . mt_rand() . '.tld/url',
            'description' => md5((string)mt_rand()),
            'state' => array_rand([
                'opened' => 'opened',
                'closed' => 'closed',
            ]),
            'confidential' => random_int(0, 1),
        ];
        Issue::create([
            'issue_id' => $id,
            'issue_iid' => random_int(1, PHP_INT_MAX),
            'project_id' => random_int(1, PHP_INT_MAX),
            'title' => 'title' . random_int(1, PHP_INT_MAX),
            'url' => 'https://webtest' . mt_rand() . '.tld/url',
            'description' => md5((string)mt_rand()),
            'state' => array_rand([
                'opened' => 'opened',
                'closed' => 'closed',
            ]),
            'confidential' => random_int(0, 1),
        ]);

        // run
        $repository = new IssueRepository();
        $result = $repository->updateOrCreate($id, $data);

        // verify/assert
        $validationData = $data;
        $validationData['issue_iid'] = $validationData['iid'];
        unset($validationData['iid']);
        $this->assertEquals($id, $result->getIssueId());
        $this->assertDatabaseHas('gitlab_issues', $validationData);
        $this->assertDatabaseCount(Issue::class, 1);
    }

    /**
     * @test
     */
    public function weCanSyncTheLabels(): void
    {
        // setup / mock
        $id = random_int(1, PHP_INT_MAX);
        $issue = Issue::create([
            'issue_id' => $id,
            'issue_iid' => random_int(1, PHP_INT_MAX),
            'project_id' => random_int(1, PHP_INT_MAX),
            'title' => 'title' . random_int(1, PHP_INT_MAX),
            'url' => 'https://webtest' . mt_rand() . '.tld/url',
            'description' => md5((string)mt_rand()),
            'state' => array_rand([
                'opened' => 'opened',
                'closed' => 'closed',
            ]),
            'confidential' => random_int(0, 1),
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
        $repository = new IssueRepository();
        $result = $repository->syncLabels($id, [LabelDTO::fromLabel($label)]);

        // verify/assert
        $this->assertNotNull($result);
        $this->assertDatabaseHas(
            'gitlab_issue_gitlab_label',
            [
                'issue_id' => $issue->id,
                'label_id' => $label->id,
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
        $repository = new IssueRepository();
        $result = $repository->syncLabels(random_int(1, PHP_INT_MAX), [LabelDTO::fromLabel($label)]);

        // verify/assert
        $this->assertNull($result);
        $this->assertDatabaseCount('gitlab_issue_gitlab_label', 0);
    }
}

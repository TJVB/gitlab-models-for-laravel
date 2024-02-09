<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Tests\Services;

use TJVB\GitlabModelsForLaravel\Contracts\Services\NoteHookHandlerContract;
use TJVB\GitlabModelsForLaravel\Services\NoteHookHandler;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\FakeGitLabHookModel;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeIssueUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeMergeRequestUpdateServiceContract;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeNoteUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\Fakes\Services\FakeProjectUpdateService;
use TJVB\GitlabModelsForLaravel\Tests\TestCase;
use function Safe\file_get_contents;
use function Safe\json_decode;

final class NoteHookHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function weImplementTheContract(): void
    {
        // run
        $handler = $this->app->make(NoteHookHandler::class);

        // verify/assert
        $this->assertInstanceOf(NoteHookHandler::class, $handler);
        $this->assertInstanceOf(NoteHookHandlerContract::class, $handler);
    }

    /**
     * @test
     * @dataProvider noteTypesDataProvider
     */
    public function weStoreTheNoteData(
        string $type,
        int $noteCount,
        int $issueCount,
        int $mrCount,
        int $projectCount
    ): void {
        // setup / mock
        $issueUpdateService = new FakeIssueUpdateService();
        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $noteUpdateService = new FakeNoteUpdateService();
        $projectUpdateService = new FakeProjectUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . $type . '.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';

        // run
        $handler = new NoteHookHandler(
            $issueUpdateService,
            $mergeRequestUpdateService,
            $noteUpdateService,
            $projectUpdateService
        );
        $handler->handle($hookModel);

        // verify/assert
        $this->assertCount($noteCount, $noteUpdateService->receivedData);
        $this->assertCount($issueCount, $issueUpdateService->receivedData);
        $this->assertCount($mrCount, $mergeRequestUpdateService->receivedData);
        $this->assertCount($projectCount, $projectUpdateService->receivedData);
    }

    /**
     * @test
     */
    public function weDontCrashOnInvalidNoteData(): void
    {
        // setup / mock
        $issueUpdateService = new FakeIssueUpdateService();
        $mergeRequestUpdateService = new FakeMergeRequestUpdateServiceContract();
        $noteUpdateService = new FakeNoteUpdateService();
        $projectUpdateService = new FakeProjectUpdateService();

        $hookModel = new FakeGitLabHookModel();
        $hookModel->body = json_decode(file_get_contents(self::EXAMPLE_PAYLOADS . 'comment_code_snippet.json'), true);
        $hookModel->objectKind = $hookModel->eventType = $hookModel->eventName = 'note';
        $hookModel->body['object_attributes'] = 'invalid attribute';
        $hookModel->body['project'] = 'invalid project';

        // run
        $handler = new NoteHookHandler(
            $issueUpdateService,
            $mergeRequestUpdateService,
            $noteUpdateService,
            $projectUpdateService
        );
        $handler->handle($hookModel);

        // verify/assert
        $this->assertEmpty($noteUpdateService->receivedData);
        $this->assertEmpty($issueUpdateService->receivedData);
        $this->assertEmpty($mergeRequestUpdateService->receivedData);
        $this->assertEmpty($projectUpdateService->receivedData);
    }

    public function noteTypesDataProvider(): array
    {
        return [
            'comment_code_snippet' => [
                'comment_code_snippet',
                1,
                0,
                0,
                1,
            ],
            'comment_commit' => [
                'comment_commit',
                1,
                0,
                0,
                1,
            ],
            'comment_issue' => [
                'comment_issue',
                1,
                1,
                0,
                1,
            ],
            'comment_merge_request' => [
                'comment_merge_request',
                1,
                0,
                1,
                1,
            ],
        ];
    }
}

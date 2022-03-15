<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Models;

interface Issue
{
    public function getIssueId(): int;
    public function getIssueIid(): int;
    public function getProjectId(): int;
    public function getTitle(): string;
    public function getDescription(): string;
    public function getUrl(): string;
    public function getState(): string;
    public function getConfidential(): bool;
}

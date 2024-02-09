<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Models;

use Carbon\CarbonImmutable;

interface MergeRequest
{
    public function getAuthorId(): int;

    public function getBlockingDiscussionsResolved(): ?bool;

    public function getCreatedAt(): CarbonImmutable;

    public function getDescription(): string;

    public function getMergeRequestId(): int;

    public function getMergeRequestIid(): int;

    public function getMergeStatus(): string;

    public function getState(): string;

    public function getSourceProjectId(): int;

    public function getSourceBranch(): string;

    public function getTargetProjectId(): int;

    public function getTargetBranch(): string;

    public function getTitle(): string;

    public function getUpdatedAt(): CarbonImmutable;

    public function getUrl(): string;

    public function getWorkInProgress(): bool;
}

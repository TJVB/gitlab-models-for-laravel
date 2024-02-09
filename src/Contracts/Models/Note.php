<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Models;

use Carbon\CarbonImmutable;

interface Note
{
    public function getAuthorId(): int;

    public function getCommitId(): ?string;

    public function getCreatedAt(): CarbonImmutable;

    public function getLineCode(): ?string;

    public function getNote(): string;

    public function getNoteId(): int;

    public function getNoteableId(): ?int;

    public function getNoteableType(): string;

    public function getProjectId(): ?int;

    public function getUpdatedAt(): CarbonImmutable;

    public function getUrl(): string;
}

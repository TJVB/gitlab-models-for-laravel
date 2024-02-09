<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Models;

use Carbon\CarbonImmutable;

interface Build
{
    public function getBuildId(): int;

    public function getName(): string;

    public function getStage(): string;

    public function getStatus(): string;

    public function getDuration(): ?float;

    public function getCreatedAt(): ?CarbonImmutable;

    public function getStartedAt(): ?CarbonImmutable;

    public function getFinishedAt(): ?CarbonImmutable;

    public function getAllowFailure(): bool;

    public function getProjectId(): int;

    public function getPipelineId(): int;
}

<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Models;

use Carbon\CarbonImmutable;

interface Pipeline
{
    public function getCreatedAt(): CarbonImmutable;
    public function getDuration(): int;
    public function getFinishedAt(): CarbonImmutable;
    public function getPipelineId(): int;
    public function getProjectId(): int;
    public function getRef(): string;
    public function getSha(): string;
    public function getSource(): string;
    public function getStages(): array;
    public function getStatus(): string;
    public function isTag(): bool;
}

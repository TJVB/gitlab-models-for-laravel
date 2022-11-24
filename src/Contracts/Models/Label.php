<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Models;

use Carbon\CarbonImmutable;

interface Label
{
    public function getLabelId(): int;
    public function getTitle(): string;
    public function getColor(): string;
    public function getCreatedAt(): ?CarbonImmutable;
    public function getUpdatedAt(): ?CarbonImmutable;
    public function getProjectId(): ?int;
    public function getDescription(): ?string;
    public function getGroupId(): ?int;
    public function getType(): string;
}

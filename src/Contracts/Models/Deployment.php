<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Models;

use Carbon\CarbonImmutable;

interface Deployment
{
    public function getDeploymentId(): int;
    public function getDeployableId(): int;
    public function getDeployableUrl(): string;
    public function getEnvironment(): string;
    public function getStatus(): string;
    public function getStatusChangedAt(): ?CarbonImmutable;
}

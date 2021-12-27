<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Models;

interface Project
{
    public function getProjectId(): int;
    public function getProjectName(): string;
    public function getWebUrl(): string;
    public function getDescription(): string;
    public function getAvatarUrl(): string;
    public function getVisibilityLevel(): int;
}

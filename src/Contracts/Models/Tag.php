<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Contracts\Models;

interface Tag
{
    public function getProjectId(): int;
    public function getRef(): string;
    public function getCheckoutSha(): string;
}

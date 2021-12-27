<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Project as ProjectContract;

/**
 * @property integer $project_id
 * @property string $name
 * @property string $web_url
 * @property string $description
 * @property string $avatar_url
 * @property integer $visibility_level
 * @method static ?Project find(int $id)
 * @method static Project updateOrCreate(array $attributes, array $values = [])
 */
class Project extends Model implements ProjectContract
{

    public function getProjectId(): int
    {
        return $this->project_id;
    }

    public function getProjectName(): string
    {
        return $this->name;
    }

    public function getWebUrl(): string
    {
        return $this->web_url;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatar_url;
    }

    public function getVisibilityLevel(): int
    {
        return $this->visibility_level;
    }
}

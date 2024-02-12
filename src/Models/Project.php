<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Project as ProjectContract;

/**
 * @property integer|string $project_id
 * @property string $name
 * @property string $web_url
 * @property ?string $description
 * @property ?string $avatar_url
 * @property integer|string $visibility_level
 * @method static ?Project firstWhere(array $filters)
 * @method static Project updateOrCreate(array $attributes, array $values = [])
 * @method static Project create(array $values)
 */
class Project extends Model implements ProjectContract
{
    use SoftDeletes;

    public $table = 'gitlab_projects';

    public $fillable = [
        'project_id',
        'name',
        'web_url',
        'description',
        'avatar_url',
        'visibility_level',
    ];

    public function getProjectId(): int
    {
        return (int) $this->project_id;
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
        return (string) $this->description;
    }

    public function getAvatarUrl(): string
    {
        return (string) $this->avatar_url;
    }

    public function getVisibilityLevel(): int
    {
        return (int) $this->visibility_level;
    }
}

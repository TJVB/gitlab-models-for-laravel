<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Issue as IssueContract;

/**
 *
 * @property integer|string $issue_id
 * @property integer|string $issue_iid
 * @property integer|string $project_id
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $state
 * @property bool $confidential
 * @method static ?Issue firstWhere(array $filters)
 * @method static Issue updateOrCreate(array $attributes, array $values = [])
 * @method static Issue create(array $values)
 */
class Issue extends Model implements IssueContract
{
    use SoftDeletes;

    public $table = 'gitlab_issues';

    public $fillable = [
        'issue_id',
        'issue_iid',
        'project_id',
        'title',
        'url',
        'description',
        'state',
        'confidential',
    ];

    public function getIssueId(): int
    {
        return (int) $this->issue_id;
    }

    public function getIssueIid(): int
    {
        return (int) $this->issue_iid;
    }

    public function getProjectId(): int
    {
        return (int) $this->project_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getConfidential(): bool
    {
        return (bool) $this->confidential;
    }
}

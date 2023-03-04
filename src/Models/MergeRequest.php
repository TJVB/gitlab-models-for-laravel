<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use TJVB\GitlabModelsForLaravel\Contracts\Models\MergeRequest as MergeRequestContract;

/**
 *
 * @property integer $id
 * @property integer $author_id
 * @property bool $blocking_discussions_resolved
 * @property ?string $description
 * @property CarbonImmutable $merge_request_created_at
 * @property integer $merge_request_id
 * @property integer $merge_request_iid
 * @property string $merge_status
 * @property CarbonImmutable $merge_request_updated_at
 * @property string $state
 * @property integer $source_project_id
 * @property string $source_branch
 * @property integer $target_project_id
 * @property string $target_branch
 * @property string $title
 * @property string $url
 * @property bool $work_in_progress
 * @method static MergeRequest updateOrCreate(array $attributes, array $values = [])
 * @method static MergeRequest create(array $values)
 */
class MergeRequest extends Model implements MergeRequestContract
{
    use SoftDeletes;

    public $table = 'gitlab_merge_requests';

    public $fillable = [
        'author_id',
        'blocking_discussions_resolved',
        'description',
        'merge_request_created_at',
        'merge_request_id',
        'merge_request_iid',
        'merge_status',
        'merge_request_updated_at',
        'state',
        'source_project_id',
        'source_branch',
        'target_project_id',
        'target_branch',
        'title',
        'url',
        'work_in_progress',
    ];

    protected $casts = [
        'author_id' => 'integer',
        'blocking_discussions_resolved' => 'boolean',
        'merge_request_created_at' => 'immutable_datetime',
        'merge_request_id' => 'integer',
        'merge_request_iid' => 'integer',
        'source_project_id' => 'integer',
        'target_project_id' => 'integer',
        'merge_request_updated_at' => 'immutable_datetime',
        'work_in_progress' => 'boolean',
    ];

    /**
     * The storage format of the model's date columns.
     * We need to update this to not lose the precision that we receive from GitLab
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function getAuthorId(): int
    {
        return $this->author_id;
    }

    public function getBlockingDiscussionsResolved(): ?bool
    {
        return $this->blocking_discussions_resolved;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->merge_request_created_at;
    }

    public function getDescription(): string
    {
        return (string)$this->description;
    }

    public function getMergeRequestId(): int
    {
        return $this->merge_request_id;
    }

    public function getMergeRequestIid(): int
    {
        return $this->merge_request_iid;
    }

    public function getMergeStatus(): string
    {
        return $this->merge_status;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getSourceProjectId(): int
    {
        return $this->source_project_id;
    }

    public function getSourceBranch(): string
    {
        return $this->source_branch;
    }

    public function getTargetProjectId(): int
    {
        return $this->target_project_id;
    }

    public function getTargetBranch(): string
    {
        return $this->target_branch;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUpdatedAt(): CarbonImmutable
    {
        return $this->merge_request_updated_at;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getWorkInProgress(): bool
    {
        return $this->work_in_progress;
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            Label::class,
            'gitlab_label_gitlab_merge_request',
        );
    }
}

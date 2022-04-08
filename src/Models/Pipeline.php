<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Pipeline as PipelineContract;

/**
 * @property integer $duration
 * @property CarbonImmutable $pipeline_created_at
 * @property CarbonImmutable $pipeline_finished_at
 * @property integer $pipeline_id
 * @property integer $pipeline_iid
 * @property string $ref
 * @property string $sha
 * @property string $source
 * @property array $stages
 * @property string $status
 * @property bool $tag
 * @method static Pipeline updateOrCreate(array $attributes, array $values = [])
 * @method static Pipeline create(array $values)
 */
class Pipeline extends Model implements PipelineContract
{
    use SoftDeletes;

    public $table = 'gitlab_pipelines';

    public $fillable = [
        'duration',
        'pipeline_created_at',
        'pipeline_finished_at',
        'pipeline_id',
        'pipeline_iid',
        'ref',
        'sha',
        'source',
        'stages',
        'status',
        'tag',
    ];

    protected $casts = [
        'pipeline_created_at' => 'immutable_datetime',
        'pipeline_finished_at' => 'immutable_datetime',
        'pipeline_id' => 'integer',
        'pipeline_iid' => 'integer',
        'stages' => 'array',
        'tag' => 'bool',
    ];

    /**
     * The storage format of the model's date columns.
     * We need to update this to not lose the precision that we receive from GitLab
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->pipeline_created_at;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getFinishedAt(): CarbonImmutable
    {
        return $this->pipeline_finished_at;
    }

    public function getPipelineId(): int
    {
        return $this->pipeline_id;
    }

    public function getPipelineIid(): int
    {
        return $this->pipeline_iid;
    }

    public function getRef(): string
    {
        return $this->ref;
    }

    public function getSha(): string
    {
        return $this->sha;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getStages(): array
    {
        return $this->stages;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isTag(): bool
    {
        return $this->tag;
    }
}

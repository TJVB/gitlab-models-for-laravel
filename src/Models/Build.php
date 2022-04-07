<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Build as BuildContract;

/**
 *
 * @property integer $build_id
 * @property integer $pipeline_id
 * @property integer $project_id
 * @property string $name
 * @property string $stage
 * @property string $status
 * @property null|float $duration
 * @property CarbonImmutable $build_created_at
 * @property null|CarbonImmutable $started_at
 * @property null|CarbonImmutable $finished_at
 * @property bool $allow_failure
 * @method static Build updateOrCreate(array $attributes, array $values = [])
 * @method static Build create(array $values)
 */
class Build extends Model implements BuildContract
{
    public $table = 'gitlab_builds';
    public $fillable = [
        'build_id',
        'pipeline_id',
        'project_id',
        'name',
        'stage',
        'status',
        'duration',
        'build_created_at',
        'started_at',
        'finished_at',
        'allow_failure',
    ];
    protected $casts = [
        'build_id' => 'integer',
        'pipeline_id' => 'integer',
        'project_id' => 'integer',
        'build_created_at' => 'immutable_datetime',
        'started_at' => 'immutable_datetime',
        'finished_at' => 'immutable_datetime',
        'allow_failure' => 'boolean',
    ];

    /**
     * The storage format of the model's date columns.
     * We need to update this to not lose the precision that we receive from GitLab
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function getBuildId(): int
    {
        return $this->build_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStage(): string
    {
        return $this->stage;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->build_created_at;
    }

    public function getStartedAt(): ?CarbonImmutable
    {
        return $this->started_at;
    }

    public function getFinishedAt(): ?CarbonImmutable
    {
        return $this->finished_at;
    }

    public function getAllowFailure(): bool
    {
        return $this->allow_failure;
    }

    public function getProjectId(): int
    {
        return $this->project_id;
    }

    public function getPipelineId(): int
    {
        return $this->pipeline_id;
    }
}

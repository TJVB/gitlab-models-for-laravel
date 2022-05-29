<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Deployment as DeploymentContract;

/**
 * @property integer $deployment_id
 * @property integer $deployable_id
 * @property string $deployable_url
 * @property string $environment
 * @property string $status
 * @property CarbonImmutable $status_changed_at
 * @method static Deployment updateOrCreate(array $attributes, array $values = [])
 * @method static Deployment create(array $values)
 */
class Deployment extends Model implements DeploymentContract
{
    use SoftDeletes;

    public $table = 'gitlab_deployments';

    public $fillable = [
        'deployment_id',
        'deployable_id',
        'deployable_url',
        'environment',
        'status',
        'status_changed_at',
    ];

    protected $casts = [
        'deployment_id' => 'integer',
        'deployable_id' => 'integer',
        'status_changed_at' => 'immutable_datetime',
    ];
/**
     * The storage format of the model's date columns.
     * We need to update this to not lose the precision that we receive from GitLab
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';
    public function getDeploymentId(): int
    {
        return $this->deployment_id;
    }

    public function getDeployableId(): int
    {
        return $this->deployable_id;
    }

    public function getDeployableUrl(): string
    {
        return $this->deployable_url;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusChangedAt(): ?CarbonImmutable
    {
        return $this->status_changed_at;
    }
}

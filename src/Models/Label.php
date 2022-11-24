<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Label as LabelContract;

/**
 * @property integer $label_id
 * @property string $title
 * @property string $color
 * @property null|integer $project_id
 * @property CarbonImmutable $label_created_at
 * @property CarbonImmutable $label_updated_at
 * @property null|string $description
 * @property string $type
 * @property null|integer $group_id
 * @method static Label updateOrCreate(array $attributes, array $values = [])
 * @method static Label create(array $values)
 */
final class Label extends Model implements LabelContract
{
    use SoftDeletes;


    public $table = 'gitlab_labels';
    public $fillable = [
        'label_id',
        'title',
        'color',
        'project_id',
        'label_created_at',
        'label_updated_at',
        'description',
        'type',
        'group_id',
    ];
    protected $casts = [
        'label_id' => 'integer',
        'project_id' => 'integer',
        'group_id' => 'integer',
        'label_created_at' => 'immutable_datetime',
        'label_updated_at' => 'immutable_datetime',
    ];
/**
     * The storage format of the model's date columns.
     * We need to update this to not lose the precision that we receive from GitLab
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';
    public function getLabelId(): int
    {
        return $this->label_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->label_created_at;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->label_updated_at;
    }

    public function getProjectId(): ?int
    {
        return $this->project_id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getGroupId(): ?int
    {
        return $this->group_id;
    }

    public function getType(): string
    {
        return $this->type;
    }
}

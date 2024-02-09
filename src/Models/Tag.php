<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Tag as TagContract;

/**
 *
 * @property integer|string $project_id
 * @property string $ref
 * @property string $checkout_sha
 * @method static Tag updateOrCreate(array $attributes, array $values = [])
 * @method static Tag create(array $values)
 */
final class Tag extends Model implements TagContract
{
    use SoftDeletes;

    public $table = 'gitlab_tags';

    public $fillable = [
        'project_id',
        'ref',
        'checkout_sha',
    ];

    public function getProjectId(): int
    {
        return (int) $this->project_id;
    }

    public function getRef(): string
    {
        return $this->ref;
    }

    public function getCheckoutSha(): string
    {
        return $this->checkout_sha;
    }
}

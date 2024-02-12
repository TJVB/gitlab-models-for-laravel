<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TJVB\GitlabModelsForLaravel\Contracts\Models\User as UserContract;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $username
 * @property string $avatar_url
 * @method static ?User firstWhere(array $filters)
 * @method static User updateOrCreate(array $attributes, array $values = [])
 * @method static User create(array $values)
 * @method static Builder whereIn(string $field, array $values)
 */
class User extends Model implements UserContract
{
    use SoftDeletes;

    public $table = 'gitlab_users';

    public $fillable = [
        'user_id',
        'name',
        'username',
        'avatar_url',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    /**
     * The storage format of the model's date columns.
     * We need to update this to not lose the precision that we receive from GitLab
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatar_url;
    }
}

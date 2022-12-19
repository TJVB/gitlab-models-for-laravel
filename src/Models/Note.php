<?php

declare(strict_types=1);

namespace TJVB\GitlabModelsForLaravel\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TJVB\GitlabModelsForLaravel\Contracts\Models\Note as NoteContract;

/**
 *
 * @property integer $author_id
 * @property ?string $commit_id
 * @property ?string $line_code
 * @property string $note
 * @property CarbonImmutable $note_created_at
 * @property integer $note_id
 * @property CarbonImmutable $note_updated_at
 * @property ?integer $noteable_id
 * @property string $noteable_type
 * @property ?integer $project_id
 * @property string $url
 * @method static Note updateOrCreate(array $attributes, array $values = [])
 * @method static Note create(array $values)
 */
class Note extends Model implements NoteContract
{
    use SoftDeletes;

    public $table = 'gitlab_notes';

    public $fillable = [
        'author_id',
        'commit_id',
        'line_code',
        'note',
        'note_created_at',
        'note_id',
        'note_updated_at',
        'noteable_id',
        'noteable_type',
        'project_id',
        'url',
    ];

    protected $casts = [
        'author_id' => 'integer',
        'note_created_at' => 'immutable_datetime',
        'note_id' => 'integer',
        'note_updated_at' => 'immutable_datetime',
        'noteable_id' => 'integer',
        'project_id' => 'integer',
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

    public function getCommitId(): ?string
    {
        return $this->commit_id;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->note_created_at;
    }

    public function getLineCode(): ?string
    {
        return $this->line_code;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function getNoteId(): int
    {
        return $this->note_id;
    }

    public function getNoteableId(): ?int
    {
        return $this->noteable_id;
    }

    public function getNoteableType(): string
    {
        return $this->noteable_type;
    }

    public function getProjectId(): ?int
    {
        return $this->project_id;
    }

    public function getUpdatedAt(): CarbonImmutable
    {
        return $this->note_updated_at;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}

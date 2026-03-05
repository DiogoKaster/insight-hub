<?php

declare(strict_types=1);

namespace InsightHub\Repository\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use InsightHub\Repository\Database\Factories\IssueFactory;

class Issue extends Model
{
    /** @use HasFactory<IssueFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'repository_id',
        'github_user_id',
        'github_id',
        'number',
        'title',
        'body',
        'state',
        'html_url',
        'closed_at',
        'github_created_at',
        'github_updated_at',
    ];

    /**
     * @return BelongsTo<Repository, $this>
     */
    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    /**
     * @return BelongsTo<GitHubUser, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(GitHubUser::class, 'github_user_id');
    }

    /**
     * @return BelongsToMany<GitHubUser, $this, Pivot>
     */
    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(GitHubUser::class, 'issue_assignees', 'issue_id', 'github_user_id');
    }

    /**
     * @return BelongsToMany<Label, $this, Pivot>
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'issue_label');
    }

    protected static function newFactory(): IssueFactory
    {
        return IssueFactory::new();
    }

    protected function casts(): array
    {
        return [
            'github_id' => 'integer',
            'number' => 'integer',
            'closed_at' => 'datetime',
            'github_created_at' => 'datetime',
            'github_updated_at' => 'datetime',
        ];
    }
}

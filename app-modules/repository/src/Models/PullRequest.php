<?php

declare(strict_types=1);

namespace InsightHub\Repository\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use InsightHub\Repository\Database\Factories\PullRequestFactory;

class PullRequest extends Model
{
    /** @use HasFactory<PullRequestFactory> */
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
        'merged_at',
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
    public function reviewers(): BelongsToMany
    {
        return $this->belongsToMany(GitHubUser::class, 'pull_request_reviewers', 'pull_request_id', 'github_user_id');
    }

    /**
     * @return BelongsToMany<Label, $this, Pivot>
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'pull_request_label');
    }

    protected static function newFactory(): PullRequestFactory
    {
        return PullRequestFactory::new();
    }

    protected function casts(): array
    {
        return [
            'github_id' => 'integer',
            'number' => 'integer',
            'merged_at' => 'datetime',
            'closed_at' => 'datetime',
            'github_created_at' => 'datetime',
            'github_updated_at' => 'datetime',
        ];
    }
}

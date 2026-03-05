<?php

declare(strict_types=1);

namespace InsightHub\Repository\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use InsightHub\Repository\Database\Factories\GitHubUserFactory;

class GitHubUser extends Model
{
    /** @use HasFactory<GitHubUserFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = 'github_users';

    protected $fillable = [
        'github_id',
        'login',
        'name',
        'email',
        'avatar_url',
        'html_url',
        'type',
    ];

    /**
     * @return HasMany<PullRequest, $this>
     */
    public function pullRequests(): HasMany
    {
        return $this->hasMany(PullRequest::class, 'github_user_id');
    }

    /**
     * @return HasMany<Issue, $this>
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'github_user_id');
    }

    /**
     * @return HasMany<Release, $this>
     */
    public function releases(): HasMany
    {
        return $this->hasMany(Release::class, 'github_user_id');
    }

    /**
     * @return BelongsToMany<Issue, $this, Pivot>
     */
    public function assignedIssues(): BelongsToMany
    {
        return $this->belongsToMany(Issue::class, 'issue_assignees', 'github_user_id', 'issue_id');
    }

    /**
     * @return BelongsToMany<PullRequest, $this, Pivot>
     */
    public function reviewedPullRequests(): BelongsToMany
    {
        return $this->belongsToMany(PullRequest::class, 'pull_request_reviewers', 'github_user_id', 'pull_request_id');
    }

    protected static function newFactory(): GitHubUserFactory
    {
        return GitHubUserFactory::new();
    }

    protected function casts(): array
    {
        return [
            'github_id' => 'integer',
        ];
    }
}

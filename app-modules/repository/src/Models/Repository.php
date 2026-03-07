<?php

declare(strict_types=1);

namespace InsightHub\Repository\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InsightHub\Repository\Database\Factories\RepositoryFactory;

class Repository extends Model
{
    /** @use HasFactory<RepositoryFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'github_id',
        'owner_login',
        'name',
        'full_name',
        'description',
        'html_url',
        'default_branch',
        'language',
        'is_private',
        'stars_count',
        'forks_count',
        'open_issues_count',
        'github_created_at',
        'github_updated_at',
    ];

    /**
     * @return HasMany<PullRequest, $this>
     */
    public function pullRequests(): HasMany
    {
        return $this->hasMany(PullRequest::class);
    }

    /**
     * @return HasMany<Issue, $this>
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    /**
     * @return HasMany<Release, $this>
     */
    public function releases(): HasMany
    {
        return $this->hasMany(Release::class);
    }

    /**
     * @return HasMany<Label, $this>
     */
    public function labels(): HasMany
    {
        return $this->hasMany(Label::class);
    }

    protected static function newFactory(): RepositoryFactory
    {
        return RepositoryFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'stars_count' => 'integer',
            'forks_count' => 'integer',
            'open_issues_count' => 'integer',
            'github_created_at' => 'datetime',
            'github_updated_at' => 'datetime',
        ];
    }
}

<?php

declare(strict_types=1);

namespace InsightHub\Repository\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InsightHub\Repository\Database\Factories\ReleaseFactory;

class Release extends Model
{
    /** @use HasFactory<ReleaseFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'repository_id',
        'github_user_id',
        'github_id',
        'tag_name',
        'name',
        'body',
        'is_draft',
        'is_prerelease',
        'html_url',
        'github_created_at',
        'github_published_at',
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

    protected static function newFactory(): ReleaseFactory
    {
        return ReleaseFactory::new();
    }

    protected function casts(): array
    {
        return [
            'github_id' => 'integer',
            'is_draft' => 'boolean',
            'is_prerelease' => 'boolean',
            'github_created_at' => 'datetime',
            'github_published_at' => 'datetime',
        ];
    }
}

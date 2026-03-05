<?php

declare(strict_types=1);

namespace InsightHub\Repository\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use InsightHub\Repository\Database\Factories\LabelFactory;

class Label extends Model
{
    /** @use HasFactory<LabelFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'repository_id',
        'github_id',
        'name',
        'color',
        'description',
    ];

    /**
     * @return BelongsTo<Repository, $this>
     */
    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    /**
     * @return BelongsToMany<Issue, $this, Pivot>
     */
    public function issues(): BelongsToMany
    {
        return $this->belongsToMany(Issue::class, 'issue_label');
    }

    /**
     * @return BelongsToMany<PullRequest, $this, Pivot>
     */
    public function pullRequests(): BelongsToMany
    {
        return $this->belongsToMany(PullRequest::class, 'pull_request_label');
    }

    protected static function newFactory(): LabelFactory
    {
        return LabelFactory::new();
    }

    protected function casts(): array
    {
        return [
            'github_id' => 'integer',
        ];
    }
}

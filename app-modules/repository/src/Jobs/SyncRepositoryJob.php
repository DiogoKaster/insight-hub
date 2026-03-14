<?php

declare(strict_types=1);

namespace InsightHub\Repository\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use InsightHub\Repository\Models\Repository;

class SyncRepositoryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public readonly Repository $repository) {}

    public function handle(): void
    {
        dispatch(new SyncPullRequestsJob($this->repository));
    }

    /** @return array<int, WithoutOverlapping> */
    public function middleware(): array
    {
        return [new WithoutOverlapping('sync-repo-'.$this->repository->id)];
    }
}

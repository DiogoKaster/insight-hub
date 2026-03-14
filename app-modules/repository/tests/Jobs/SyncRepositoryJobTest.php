<?php

declare(strict_types=1);

use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Queue;
use InsightHub\Repository\Jobs\SyncPullRequestsJob;
use InsightHub\Repository\Jobs\SyncRepositoryJob;
use InsightHub\Repository\Models\Repository;

beforeEach(function (): void {
    Queue::fake();
});

it('dispatches SyncPullRequestsJob with page 1', function (): void {
    $repository = Repository::factory()->create();

    new SyncRepositoryJob($repository)->handle();

    Queue::assertPushed(SyncPullRequestsJob::class, fn ($job) => $job->page === 1 && $job->repository->is($repository));
});

it('does not double-dispatch for same repository via WithoutOverlapping middleware', function (): void {
    $repository = Repository::factory()->create();

    $job = new SyncRepositoryJob($repository);
    $middleware = $job->middleware();

    expect($middleware)->toHaveCount(1)
        ->and($middleware[0])->toBeInstanceOf(WithoutOverlapping::class);
});

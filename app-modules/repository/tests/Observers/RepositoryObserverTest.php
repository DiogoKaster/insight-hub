<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use InsightHub\Repository\Jobs\SyncRepositoryJob;
use InsightHub\Repository\Models\Repository;

beforeEach(function (): void {
    Queue::fake();
});

it('dispatches SyncRepositoryJob when a repository is created', function (): void {
    Repository::factory()->create();

    Queue::assertPushed(SyncRepositoryJob::class);
});

it('does not dispatch SyncRepositoryJob on update', function (): void {
    $repository = Repository::factory()->create();
    Queue::fake(); // reset after creation

    $repository->update(['name' => 'updated-name']);

    Queue::assertNotPushed(SyncRepositoryJob::class);
});

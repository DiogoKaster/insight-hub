<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use InsightHub\Repository\Jobs\SyncPullRequestDetailsJob;
use InsightHub\Repository\Jobs\SyncPullRequestsJob;
use InsightHub\Repository\Models\GitHubUser;
use InsightHub\Repository\Models\PullRequest;
use InsightHub\Repository\Models\Repository;

beforeEach(function (): void {
    Http::preventStrayRequests();
    Queue::fake();
});

function makePrData(int $number, string $updatedAt = '2026-03-01T00:00:00Z'): array
{
    return [
        'id' => $number * 100,
        'number' => $number,
        'title' => 'PR #'.$number,
        'body' => 'body',
        'state' => 'open',
        'html_url' => 'https://github.com/acme/widget/pull/'.$number,
        'merged_at' => null,
        'closed_at' => null,
        'created_at' => '2026-01-01T00:00:00Z',
        'updated_at' => $updatedAt,
        'user' => [
            'id' => 42,
            'login' => 'alice',
            'avatar_url' => 'https://avatars.githubusercontent.com/u/42',
            'html_url' => 'https://github.com/alice',
            'type' => 'User',
        ],
    ];
}

it('upserts pull requests from API response', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls*' => Http::response([makePrData(1)]),
    ]);

    new SyncPullRequestsJob($repository)->handle();

    expect(PullRequest::where('repository_id', $repository->id)->where('number', 1)->exists())->toBeTrue();
    expect(GitHubUser::where('github_id', 42)->exists())->toBeTrue();
});

it('dispatches SyncPullRequestDetailsJob for each PR', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls*' => Http::response([
            makePrData(1),
            makePrData(2),
        ]),
    ]);

    new SyncPullRequestsJob($repository)->handle();

    Queue::assertPushed(SyncPullRequestDetailsJob::class, 2);
});

it('dispatches next page job when 100 results returned and last is within cutoff', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);

    $prs = array_map(fn (int $i) => makePrData($i, '2026-02-01T00:00:00Z'), range(1, 100));

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls*' => Http::response($prs),
    ]);

    $cutoff = Date::parse('2026-01-01T00:00:00Z');
    new SyncPullRequestsJob($repository, 1, $cutoff)->handle();

    Queue::assertPushed(SyncPullRequestsJob::class, fn ($job) => $job->page === 2);
});

it('stops pagination when last item is older than cutoff', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);

    $prs = array_map(fn (int $i) => makePrData($i, '2025-01-01T00:00:00Z'), range(1, 100));

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls*' => Http::response($prs),
    ]);

    $cutoff = Date::parse('2026-01-01T00:00:00Z');
    new SyncPullRequestsJob($repository, 1, $cutoff)->handle();

    Queue::assertNotPushed(SyncPullRequestsJob::class);
});

it('stops pagination when fewer than 100 results returned', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls*' => Http::response([makePrData(1)]),
    ]);

    new SyncPullRequestsJob($repository)->handle();

    Queue::assertNotPushed(SyncPullRequestsJob::class);
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use InsightHub\Repository\Jobs\SyncPullRequestDetailsJob;
use InsightHub\Repository\Models\PullRequest;
use InsightHub\Repository\Models\Repository;

beforeEach(function (): void {
    Http::preventStrayRequests();
});

function makeDetailResponse(array $overrides = []): array
{
    return array_merge([
        'number' => 1,
        'draft' => true,
        'additions' => 100,
        'deletions' => 20,
        'changed_files' => 5,
        'commits' => 3,
        'comments' => 4,
        'review_comments' => 2,
    ], $overrides);
}

it('updates analytics fields on the pull request', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);
    $pr = PullRequest::factory()->for($repository)->create(['number' => 1]);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls/1' => Http::response(makeDetailResponse()),
        'https://api.github.com/repos/acme/widget/pulls/1/reviews*' => Http::response([]),
    ]);

    new SyncPullRequestDetailsJob($pr)->handle();

    $pr->refresh();
    expect($pr->draft)->toBeTrue()
        ->and($pr->additions)->toBe(100)
        ->and($pr->deletions)->toBe(20)
        ->and($pr->changed_files)->toBe(5)
        ->and($pr->commits_count)->toBe(3)
        ->and($pr->comments_count)->toBe(4)
        ->and($pr->review_comments_count)->toBe(2);
});

it('syncs reviewers with correct state', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);
    $pr = PullRequest::factory()->for($repository)->create(['number' => 1]);

    $reviews = [
        [
            'id' => 1,
            'state' => 'APPROVED',
            'submitted_at' => '2026-03-01T10:00:00Z',
            'user' => ['id' => 99, 'login' => 'bob', 'avatar_url' => 'https://avatars.githubusercontent.com/u/99', 'html_url' => 'https://github.com/bob', 'type' => 'User'],
        ],
    ];

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls/1' => Http::response(makeDetailResponse()),
        'https://api.github.com/repos/acme/widget/pulls/1/reviews*' => Http::response($reviews),
    ]);

    new SyncPullRequestDetailsJob($pr)->handle();

    $pr->refresh();
    expect($pr->reviewers)->toHaveCount(1);

    $pivotState = $pr->reviewers->first()->pivot->state;
    expect($pivotState)->toBe('APPROVED');
});

it('picks most recent state when reviewer submits multiple reviews', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);
    $pr = PullRequest::factory()->for($repository)->create(['number' => 1]);

    $reviews = [
        [
            'id' => 1,
            'state' => 'CHANGES_REQUESTED',
            'submitted_at' => '2026-03-01T10:00:00Z',
            'user' => ['id' => 99, 'login' => 'bob', 'avatar_url' => 'https://avatars.githubusercontent.com/u/99', 'html_url' => 'https://github.com/bob', 'type' => 'User'],
        ],
        [
            'id' => 2,
            'state' => 'APPROVED',
            'submitted_at' => '2026-03-02T10:00:00Z',
            'user' => ['id' => 99, 'login' => 'bob', 'avatar_url' => 'https://avatars.githubusercontent.com/u/99', 'html_url' => 'https://github.com/bob', 'type' => 'User'],
        ],
    ];

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls/1' => Http::response(makeDetailResponse()),
        'https://api.github.com/repos/acme/widget/pulls/1/reviews*' => Http::response($reviews),
    ]);

    new SyncPullRequestDetailsJob($pr)->handle();

    $pr->refresh();
    expect($pr->reviewers)->toHaveCount(1);

    $pivotState = $pr->reviewers->first()->pivot->state;
    expect($pivotState)->toBe('APPROVED');
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use InsightHub\Repository\Models\Repository;
use InsightHub\Repository\Services\GitHubClient;

beforeEach(function (): void {
    Http::preventStrayRequests();
});

it('fetches pull requests with correct endpoint and params', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls*' => Http::response([
            ['number' => 1, 'title' => 'Fix bug', 'user' => ['id' => 42]],
        ]),
    ]);

    $client = new GitHubClient($repository);
    $result = $client->pullRequests(page: 1);

    expect($result)->toBeArray()->toHaveCount(1);
    Http::assertSent(fn ($request): bool => str_contains((string) $request->url(), '/repos/acme/widget/pulls')
        && $request['state'] === 'all'
        && $request['per_page'] === 100
        && $request['page'] === 1);
});

it('fetches pull request detail', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls/7' => Http::response([
            'number' => 7, 'additions' => 10, 'deletions' => 2,
        ]),
    ]);

    $client = new GitHubClient($repository);
    $result = $client->pullRequestDetail(7);

    expect($result['number'])->toBe(7);
    Http::assertSent(fn ($request) => str_contains((string) $request->url(), '/repos/acme/widget/pulls/7'));
});

it('fetches pull request reviews', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls/7/reviews*' => Http::response([
            ['id' => 1, 'state' => 'APPROVED', 'user' => ['id' => 5]],
        ]),
    ]);

    $client = new GitHubClient($repository);
    $result = $client->pullRequestReviews(7);

    expect($result)->toHaveCount(1);
    Http::assertSent(fn ($request): bool => str_contains((string) $request->url(), '/pulls/7/reviews')
        && $request['per_page'] === 100);
});

it('throws RuntimeException on non-2xx response', function (): void {
    $repository = Repository::factory()->create(['full_name' => 'acme/widget']);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls*' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    $client = new GitHubClient($repository);

    expect(fn () => $client->pullRequests())->toThrow(RuntimeException::class);
});

it('uses repository token when set', function (): void {
    $repository = Repository::factory()->create([
        'full_name' => 'acme/widget',
        'github_token' => 'repo-level-token',
    ]);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls*' => Http::response([]),
    ]);

    $client = new GitHubClient($repository);
    $client->pullRequests();

    Http::assertSent(fn ($request) => $request->header('Authorization')[0] === 'Bearer repo-level-token');
});

it('falls back to config token when repository token is null', function (): void {
    config(['services.github.token' => 'config-level-token']);

    $repository = Repository::factory()->create([
        'full_name' => 'acme/widget',
        'github_token' => null,
    ]);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls*' => Http::response([]),
    ]);

    $client = new GitHubClient($repository);
    $client->pullRequests();

    Http::assertSent(fn ($request) => $request->header('Authorization')[0] === 'Bearer config-level-token');
});

it('derives owner and repo from html_url when full_name is absent', function (): void {
    $repository = Repository::factory()->create([
        'full_name' => null,
        'html_url' => 'https://github.com/acme/widget',
    ]);

    Http::fake([
        'https://api.github.com/repos/acme/widget/pulls*' => Http::response([]),
    ]);

    $client = new GitHubClient($repository);
    $client->pullRequests();

    Http::assertSent(fn ($request) => str_contains((string) $request->url(), '/repos/acme/widget/pulls'));
});

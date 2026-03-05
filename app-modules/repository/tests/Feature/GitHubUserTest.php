<?php

declare(strict_types=1);

use InsightHub\Repository\Models\GitHubUser;
use InsightHub\Repository\Models\Issue;
use InsightHub\Repository\Models\PullRequest;
use InsightHub\Repository\Models\Repository;

it('can be created with valid data', function (): void {
    $user = GitHubUser::factory()->create();

    expect($user)->toBeInstanceOf(GitHubUser::class)
        ->and($user->id)->toBeString()
        ->and($user->login)->toBeString()
        ->and($user->type)->toBe('User');
});

it('uses uuid as primary key', function (): void {
    $user = GitHubUser::factory()->create();

    expect($user->id)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});

it('can be a bot', function (): void {
    $bot = GitHubUser::factory()->bot()->create();

    expect($bot->type)->toBe('Bot');
});

it('has many pull requests as author', function (): void {
    $repository = Repository::factory()->create();
    $user = GitHubUser::factory()->create();
    PullRequest::factory()->count(2)->for($repository)->for($user, 'author')->create();

    expect($user->pullRequests)->toHaveCount(2);
});

it('has many issues as author', function (): void {
    $repository = Repository::factory()->create();
    $user = GitHubUser::factory()->create();
    Issue::factory()->count(3)->for($repository)->for($user, 'author')->create();

    expect($user->issues)->toHaveCount(3);
});

it('belongs to many issues as assignee', function (): void {
    $repository = Repository::factory()->create();
    $user = GitHubUser::factory()->create();
    $issues = Issue::factory()->count(2)->for($repository)->create();
    $user->assignedIssues()->attach($issues);

    expect($user->assignedIssues)->toHaveCount(2);
});

it('belongs to many pull requests as reviewer', function (): void {
    $repository = Repository::factory()->create();
    $user = GitHubUser::factory()->create();
    $prs = PullRequest::factory()->count(2)->for($repository)->create();
    $user->reviewedPullRequests()->attach($prs);

    expect($user->reviewedPullRequests)->toHaveCount(2);
});

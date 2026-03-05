<?php

declare(strict_types=1);

use InsightHub\Repository\Models\GitHubUser;
use InsightHub\Repository\Models\Label;
use InsightHub\Repository\Models\PullRequest;
use InsightHub\Repository\Models\Repository;

it('can be created with valid data', function (): void {
    $pr = PullRequest::factory()->create();

    expect($pr)->toBeInstanceOf(PullRequest::class)
        ->and($pr->id)->toBeString()
        ->and($pr->title)->toBeString()
        ->and($pr->state)->toBeIn(['open', 'closed', 'merged']);
});

it('uses uuid as primary key', function (): void {
    $pr = PullRequest::factory()->create();

    expect($pr->id)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});

it('belongs to a repository', function (): void {
    $repository = Repository::factory()->create();
    $pr = PullRequest::factory()->for($repository)->create();

    expect($pr->repository->id)->toBe($repository->id);
});

it('belongs to an author', function (): void {
    $repository = Repository::factory()->create();
    $user = GitHubUser::factory()->create();
    $pr = PullRequest::factory()->for($repository)->for($user, 'author')->create();

    expect($pr->author->id)->toBe($user->id);
});

it('belongs to many reviewers', function (): void {
    $repository = Repository::factory()->create();
    $pr = PullRequest::factory()->for($repository)->create();
    $reviewers = GitHubUser::factory()->count(2)->create();
    $pr->reviewers()->attach($reviewers);

    expect($pr->reviewers)->toHaveCount(2);
});

it('belongs to many labels', function (): void {
    $repository = Repository::factory()->create();
    $pr = PullRequest::factory()->for($repository)->create();
    $labels = Label::factory()->count(2)->for($repository)->create();
    $pr->labels()->attach($labels);

    expect($pr->labels)->toHaveCount(2);
});

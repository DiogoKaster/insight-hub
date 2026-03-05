<?php

declare(strict_types=1);

use InsightHub\Repository\Models\GitHubUser;
use InsightHub\Repository\Models\Issue;
use InsightHub\Repository\Models\Label;
use InsightHub\Repository\Models\Repository;

it('can be created with valid data', function (): void {
    $issue = Issue::factory()->create();

    expect($issue)->toBeInstanceOf(Issue::class)
        ->and($issue->id)->toBeString()
        ->and($issue->title)->toBeString()
        ->and($issue->state)->toBeIn(['open', 'closed']);
});

it('uses uuid as primary key', function (): void {
    $issue = Issue::factory()->create();

    expect($issue->id)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});

it('belongs to a repository', function (): void {
    $repository = Repository::factory()->create();
    $issue = Issue::factory()->for($repository)->create();

    expect($issue->repository->id)->toBe($repository->id);
});

it('belongs to an author', function (): void {
    $repository = Repository::factory()->create();
    $user = GitHubUser::factory()->create();
    $issue = Issue::factory()->for($repository)->for($user, 'author')->create();

    expect($issue->author->id)->toBe($user->id);
});

it('belongs to many assignees', function (): void {
    $repository = Repository::factory()->create();
    $issue = Issue::factory()->for($repository)->create();
    $assignees = GitHubUser::factory()->count(3)->create();
    $issue->assignees()->attach($assignees);

    expect($issue->assignees)->toHaveCount(3);
});

it('belongs to many labels', function (): void {
    $repository = Repository::factory()->create();
    $issue = Issue::factory()->for($repository)->create();
    $labels = Label::factory()->count(2)->for($repository)->create();
    $issue->labels()->attach($labels);

    expect($issue->labels)->toHaveCount(2);
});

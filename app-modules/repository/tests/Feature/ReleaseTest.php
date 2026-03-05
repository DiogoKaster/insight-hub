<?php

declare(strict_types=1);

use InsightHub\Repository\Models\GitHubUser;
use InsightHub\Repository\Models\Release;
use InsightHub\Repository\Models\Repository;

it('can be created with valid data', function (): void {
    $release = Release::factory()->create();

    expect($release)->toBeInstanceOf(Release::class)
        ->and($release->id)->toBeString()
        ->and($release->tag_name)->toBeString()
        ->and($release->is_draft)->toBeFalse()
        ->and($release->is_prerelease)->toBeFalse();
});

it('uses uuid as primary key', function (): void {
    $release = Release::factory()->create();

    expect($release->id)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});

it('belongs to a repository', function (): void {
    $repository = Repository::factory()->create();
    $release = Release::factory()->for($repository)->create();

    expect($release->repository->id)->toBe($repository->id);
});

it('belongs to an author', function (): void {
    $repository = Repository::factory()->create();
    $user = GitHubUser::factory()->create();
    $release = Release::factory()->for($repository)->for($user, 'author')->create();

    expect($release->author->id)->toBe($user->id);
});

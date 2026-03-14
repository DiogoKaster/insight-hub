<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use InsightHub\Repository\Models\Issue;
use InsightHub\Repository\Models\Label;
use InsightHub\Repository\Models\PullRequest;
use InsightHub\Repository\Models\Release;
use InsightHub\Repository\Models\Repository;

it('can be created with valid data', function (): void {
    $repository = Repository::factory()->create();

    expect($repository)->toBeInstanceOf(Repository::class)
        ->and($repository->id)->toBeString()
        ->and($repository->github_id)->toBeInt()
        ->and($repository->full_name)->toBeString();
});

it('uses uuid as primary key', function (): void {
    $repository = Repository::factory()->create();

    expect($repository->id)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});

it('has many pull requests', function (): void {
    $repository = Repository::factory()->create();
    PullRequest::factory()->count(3)->for($repository)->create();

    expect($repository->pullRequests)->toHaveCount(3);
});

it('has many issues', function (): void {
    $repository = Repository::factory()->create();
    Issue::factory()->count(2)->for($repository)->create();

    expect($repository->issues)->toHaveCount(2);
});

it('has many releases', function (): void {
    $repository = Repository::factory()->create();
    Release::factory()->count(4)->for($repository)->create();

    expect($repository->releases)->toHaveCount(4);
});

it('has many labels', function (): void {
    $repository = Repository::factory()->create();
    Label::factory()->count(5)->for($repository)->create();

    expect($repository->labels)->toHaveCount(5);
});

it('stores github_token encrypted', function (): void {
    $repository = Repository::factory()->create(['github_token' => 'ghp_supersecret']);

    $fresh = Repository::find($repository->id);
    expect($fresh->github_token)->toBe('ghp_supersecret');

    $raw = DB::table('repositories')
        ->where('id', $repository->id)
        ->value('github_token');
    expect($raw)->not->toBe('ghp_supersecret');
});

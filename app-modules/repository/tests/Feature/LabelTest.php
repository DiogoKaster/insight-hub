<?php

declare(strict_types=1);

use InsightHub\Repository\Models\Issue;
use InsightHub\Repository\Models\Label;
use InsightHub\Repository\Models\PullRequest;
use InsightHub\Repository\Models\Repository;

it('can be created with valid data', function (): void {
    $label = Label::factory()->create();

    expect($label)->toBeInstanceOf(Label::class)
        ->and($label->id)->toBeString()
        ->and($label->name)->toBeString()
        ->and($label->color)->toBeString();
});

it('uses uuid as primary key', function (): void {
    $label = Label::factory()->create();

    expect($label->id)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});

it('belongs to a repository', function (): void {
    $repository = Repository::factory()->create();
    $label = Label::factory()->for($repository)->create();

    expect($label->repository->id)->toBe($repository->id);
});

it('belongs to many issues', function (): void {
    $repository = Repository::factory()->create();
    $label = Label::factory()->for($repository)->create();
    $issues = Issue::factory()->count(2)->for($repository)->create();
    $label->issues()->attach($issues);

    expect($label->issues)->toHaveCount(2);
});

it('belongs to many pull requests', function (): void {
    $repository = Repository::factory()->create();
    $label = Label::factory()->for($repository)->create();
    $prs = PullRequest::factory()->count(3)->for($repository)->create();
    $label->pullRequests()->attach($prs);

    expect($label->pullRequests)->toHaveCount(3);
});

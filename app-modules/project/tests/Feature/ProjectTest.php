<?php

declare(strict_types=1);

use InsightHub\Project\Models\Project;

it('can be created with valid data', function (): void {
    $project = Project::factory()->create();

    expect($project)->toBeInstanceOf(Project::class)
        ->and($project->name)->toBeString()
        ->and($project->description)->toBeString();
});

it('uses uuid as primary key', function (): void {
    $project = Project::factory()->create();

    expect($project->id)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});

it('has fillable name and description', function (): void {
    $project = Project::factory()->create([
        'name' => 'My Project',
        'description' => 'A test project.',
    ]);

    expect($project->name)->toBe('My Project')
        ->and($project->description)->toBe('A test project.');
});

it('description is optional', function (): void {
    $project = Project::factory()->create(['description' => null]);

    expect($project->description)->toBeNull();
});

it('factory creates a valid instance', function (): void {
    $project = Project::factory()->make();

    expect($project)->toBeInstanceOf(Project::class)
        ->and($project->name)->not->toBeEmpty();
});

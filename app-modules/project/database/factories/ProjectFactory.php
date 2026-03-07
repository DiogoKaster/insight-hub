<?php

declare(strict_types=1);

namespace InsightHub\Project\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use InsightHub\Project\Models\Project;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
        ];
    }
}

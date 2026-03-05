<?php

declare(strict_types=1);

namespace InsightHub\Repository\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use InsightHub\Repository\Models\Label;
use InsightHub\Repository\Models\Repository;

/**
 * @extends Factory<Label>
 */
class LabelFactory extends Factory
{
    protected $model = Label::class;

    public function definition(): array
    {
        return [
            'repository_id' => Repository::factory(),
            'github_id' => fake()->unique()->numberBetween(1, 999999999),
            'name' => fake()->word(),
            'color' => fake()->hexColor(),
            'description' => fake()->optional()->sentence(),
        ];
    }
}

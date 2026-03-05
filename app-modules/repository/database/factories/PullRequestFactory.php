<?php

declare(strict_types=1);

namespace InsightHub\Repository\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use InsightHub\Repository\Models\PullRequest;
use InsightHub\Repository\Models\Repository;

/**
 * @extends Factory<PullRequest>
 */
class PullRequestFactory extends Factory
{
    protected $model = PullRequest::class;

    public function definition(): array
    {
        return [
            'repository_id' => Repository::factory(),
            'github_user_id' => null,
            'github_id' => fake()->unique()->numberBetween(1, 999999999),
            'number' => fake()->numberBetween(1, 9999),
            'title' => fake()->sentence(),
            'body' => fake()->optional()->paragraphs(3, true),
            'state' => fake()->randomElement(['open', 'closed', 'merged']),
            'html_url' => fake()->url(),
            'merged_at' => null,
            'closed_at' => null,
            'github_created_at' => fake()->dateTimeBetween('-2 years', '-1 month'),
            'github_updated_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}

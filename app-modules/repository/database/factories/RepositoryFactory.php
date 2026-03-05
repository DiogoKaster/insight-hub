<?php

declare(strict_types=1);

namespace InsightHub\Repository\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use InsightHub\Repository\Models\Repository;

/**
 * @extends Factory<Repository>
 */
class RepositoryFactory extends Factory
{
    protected $model = Repository::class;

    public function definition(): array
    {
        $owner = fake()->userName();
        $name = fake()->slug(2, false);

        return [
            'github_id' => fake()->unique()->numberBetween(1, 999999999),
            'owner_login' => $owner,
            'name' => $name,
            'full_name' => $owner.'/'.$name,
            'description' => fake()->optional()->sentence(),
            'html_url' => 'https://github.com/'.$owner.'/'.$name,
            'default_branch' => 'main',
            'language' => fake()->optional()->randomElement(['PHP', 'TypeScript', 'Python', 'Go', 'Rust']),
            'is_private' => false,
            'stars_count' => fake()->numberBetween(0, 10000),
            'forks_count' => fake()->numberBetween(0, 1000),
            'open_issues_count' => fake()->numberBetween(0, 500),
            'github_created_at' => fake()->dateTimeBetween('-5 years', '-1 year'),
            'github_updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

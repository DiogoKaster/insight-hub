<?php

declare(strict_types=1);

namespace InsightHub\Repository\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use InsightHub\Repository\Models\GitHubUser;

/**
 * @extends Factory<GitHubUser>
 */
class GitHubUserFactory extends Factory
{
    protected $model = GitHubUser::class;

    public function definition(): array
    {
        $login = fake()->unique()->userName();

        return [
            'github_id' => fake()->unique()->numberBetween(1, 999999999),
            'login' => $login,
            'name' => fake()->optional()->name(),
            'email' => fake()->optional()->safeEmail(),
            'avatar_url' => 'https://avatars.githubusercontent.com/u/'.fake()->numberBetween(1, 999999999),
            'html_url' => 'https://github.com/'.$login,
            'type' => 'User',
        ];
    }

    public function bot(): static
    {
        return $this->state(['type' => 'Bot']);
    }
}

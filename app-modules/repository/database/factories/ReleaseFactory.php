<?php

declare(strict_types=1);

namespace InsightHub\Repository\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use InsightHub\Repository\Models\Release;
use InsightHub\Repository\Models\Repository;

/**
 * @extends Factory<Release>
 */
class ReleaseFactory extends Factory
{
    protected $model = Release::class;

    public function definition(): array
    {
        $major = fake()->numberBetween(0, 5);
        $minor = fake()->numberBetween(0, 20);
        $patch = fake()->numberBetween(0, 50);

        return [
            'repository_id' => Repository::factory(),
            'github_user_id' => null,
            'github_id' => fake()->unique()->numberBetween(1, 999999999),
            'tag_name' => sprintf('v%d.%d.%d', $major, $minor, $patch),
            'name' => fake()->optional()->sentence(4),
            'body' => fake()->optional()->paragraphs(2, true),
            'is_draft' => false,
            'is_prerelease' => false,
            'html_url' => fake()->url(),
            'github_created_at' => fake()->dateTimeBetween('-2 years', '-1 month'),
            'github_published_at' => fake()->optional()->dateTimeBetween('-2 years', '-1 month'),
        ];
    }
}

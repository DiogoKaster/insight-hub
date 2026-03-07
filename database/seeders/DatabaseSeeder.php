<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use InsightHub\Project\Models\Project;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@insighthub.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ],
        );

        Project::firstOrCreate(
            ['name' => 'Default Project'],
            ['description' => 'My first project'],
        );
    }
}

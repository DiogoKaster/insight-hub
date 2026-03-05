<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repositories', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('github_id')->unique();
            $table->string('owner_login');
            $table->string('name');
            $table->string('full_name')->unique();
            $table->text('description')->nullable();
            $table->string('html_url');
            $table->string('default_branch');
            $table->string('language')->nullable();
            $table->boolean('is_private')->default(false);
            $table->integer('stars_count')->default(0);
            $table->integer('forks_count')->default(0);
            $table->integer('open_issues_count')->default(0);
            $table->timestamp('github_created_at');
            $table->timestamp('github_updated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repositories');
    }
};

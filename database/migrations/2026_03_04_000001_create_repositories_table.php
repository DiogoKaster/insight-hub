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
            $table->unsignedBigInteger('github_id')->nullable()->unique();
            $table->string('owner_login')->nullable();
            $table->string('name');
            $table->string('full_name')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('html_url')->nullable();
            $table->string('default_branch')->nullable();
            $table->string('language')->nullable();
            $table->boolean('is_private')->default(false);
            $table->integer('stars_count')->default(0);
            $table->integer('forks_count')->default(0);
            $table->integer('open_issues_count')->default(0);
            $table->timestamp('github_created_at')->nullable();
            $table->timestamp('github_updated_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repositories');
    }
};

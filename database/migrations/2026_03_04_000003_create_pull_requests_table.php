<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pull_requests', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('repository_id')->constrained();
            $table->foreignUuid('github_user_id')->nullable()->constrained('github_users');
            $table->unsignedBigInteger('github_id');
            $table->integer('number');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('state');
            $table->boolean('draft')->default(false);
            $table->unsignedInteger('additions')->nullable();
            $table->unsignedInteger('deletions')->nullable();
            $table->unsignedInteger('changed_files')->nullable();
            $table->unsignedInteger('commits_count')->nullable();
            $table->unsignedInteger('comments_count')->nullable();
            $table->unsignedInteger('review_comments_count')->nullable();
            $table->string('html_url');
            $table->timestamp('merged_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('github_created_at');
            $table->timestamp('github_updated_at');
            $table->timestamps();
            $table->unique(['repository_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pull_requests');
    }
};

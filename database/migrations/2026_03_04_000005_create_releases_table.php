<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('repository_id')->constrained();
            $table->foreignUuid('github_user_id')->nullable()->constrained('github_users');
            $table->unsignedBigInteger('github_id')->unique();
            $table->string('tag_name');
            $table->string('name')->nullable();
            $table->text('body')->nullable();
            $table->boolean('is_draft')->default(false);
            $table->boolean('is_prerelease')->default(false);
            $table->string('html_url');
            $table->timestamp('github_created_at');
            $table->timestamp('github_published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};

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

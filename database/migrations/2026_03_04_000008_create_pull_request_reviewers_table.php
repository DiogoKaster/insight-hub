<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pull_request_reviewers', function (Blueprint $table): void {
            $table->foreignUuid('pull_request_id')->constrained();
            $table->foreignUuid('github_user_id')->constrained('github_users');
            $table->primary(['pull_request_id', 'github_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pull_request_reviewers');
    }
};

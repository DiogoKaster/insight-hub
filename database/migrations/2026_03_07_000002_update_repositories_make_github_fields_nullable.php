<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repositories', function (Blueprint $table): void {
            $table->unsignedBigInteger('github_id')->nullable()->change();
            $table->string('html_url')->nullable()->change();
            $table->string('default_branch')->nullable()->change();
            $table->timestamp('github_created_at')->nullable()->change();
            $table->timestamp('github_updated_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('repositories', function (Blueprint $table): void {
            $table->unsignedBigInteger('github_id')->nullable(false)->change();
            $table->string('html_url')->nullable(false)->change();
            $table->string('default_branch')->nullable(false)->change();
            $table->timestamp('github_created_at')->nullable(false)->change();
            $table->timestamp('github_updated_at')->nullable(false)->change();
        });
    }
};

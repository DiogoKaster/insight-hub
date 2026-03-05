<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labels', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('repository_id')->constrained();
            $table->unsignedBigInteger('github_id');
            $table->string('name');
            $table->string('color');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->unique(['repository_id', 'github_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labels');
    }
};

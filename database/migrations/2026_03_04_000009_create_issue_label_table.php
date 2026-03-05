<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_label', function (Blueprint $table): void {
            $table->foreignUuid('issue_id')->constrained();
            $table->foreignUuid('label_id')->constrained();
            $table->primary(['issue_id', 'label_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_label');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('content_items')->restrictOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('content_version');
            $table->string('status', 20);
            $table->text('comment')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Append-only history: rows are never edited.
            $table->index(['content_id', 'content_version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_approvals');
    }
};

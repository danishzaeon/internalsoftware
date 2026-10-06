<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            // Added after design approval: works like tasks.assigned_to.
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 255);
            $table->string('content_type', 20);
            $table->longText('content')->nullable();
            $table->timestamp('scheduled_for')->nullable()->index();
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index(['assigned_to', 'status']); // staff "my content" queue
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_items');
    }
};

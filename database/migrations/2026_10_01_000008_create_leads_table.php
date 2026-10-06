<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('company_name', 200)->nullable();
            $table->string('email', 190)->nullable()->index();
            $table->string('phone', 30)->nullable();
            $table->string('source', 60)->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->text('requirement')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('expected_value', 12, 2)->nullable();
            $table->timestamp('next_followup_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->foreignId('converted_client_id')->nullable()->constrained('clients')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

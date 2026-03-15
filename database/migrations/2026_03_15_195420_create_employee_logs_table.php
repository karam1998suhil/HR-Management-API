<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_logs', function (Blueprint $table) {
            $table->id();

            // nullable so the log stays even if the employee gets deleted
            $table->foreignId('employee_id')
                  ->nullable()
                  ->constrained('employees')
                  ->nullOnDelete();

            $table->string('action');        // created, updated, deleted, imported, exported
            $table->text('description')->nullable();
            $table->json('meta')->nullable(); // stores before/after data
            $table->timestamp('logged_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_logs');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // add position_id to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('position_id')
                  ->nullable()
                  ->after('is_founder')
                  ->constrained('positions')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Position::class);
            $table->dropColumn('position_id');
        });

        Schema::dropIfExists('positions');
    }
};
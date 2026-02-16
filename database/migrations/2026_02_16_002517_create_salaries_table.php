<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('effective_from'); // Buat mencatat kapan gaji mulai berlaku // Bisa digunakan untuk histori gaji
            $table->date('effective_to')->nullable(); // NULL = current salary
            $table->text('notes')->nullable();
            $table->timestamps();
            // Belajar Query mini
            $table->index(['user_id', 'effective_from', 'effective_to']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};

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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // Foreign key to users table
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');

            // Check in/out times
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->decimal('total_jam_kerja', 5, 2)->nullable(); // Total hours worked
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha', 'libur', 'cuti'])->default('alpha');

            // Dashboard tambahan info untuk keperluan laporan
            $table->text('keterangan')->nullable();
            $table->string('photo_check_in')->nullable();
            // $table->string('location_check_in')->nullable(); // GPS coordinates //gaperlu

            $table->unique(['user_id', 'tanggal']); //duplicate check
            $table->index(['user_id', 'tanggal']);
            $table->index('tanggal');
            $table->index('status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

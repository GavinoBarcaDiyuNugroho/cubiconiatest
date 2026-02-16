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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Foreign key ke users
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Data
            $table->enum('tipe', ['cuti', 'izin', 'sakit']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('total_hari'); // Jumlah hari kerja

            // dokumen dan tambahan kalau nutut ditambahin
            $table->text('alasan');
            $table->string('dokumen_pendukung')->nullable(); // Pathing 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};

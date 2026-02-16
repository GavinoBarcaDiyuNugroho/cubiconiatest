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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Tambahan
            $table->string('nik_npwp', 50)->unique();
            $table->string('phone', 20)->nullable();
            $table->enum('nikah', ['belum_menikah', 'menikah'])->default('belum_menikah');
            $table->integer('jumlah_keluarga')->default(0);
            $table->string('kartu_keluarga_path')->nullable(); //kalau sempet registrasi upload kk pdf
            $table->string('photo_path')->nullable();

            // Logic Employment
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete(); // Foreign key ke roles
            $table->foreignId('jobdesk_id')->constrained('jobdesks')->restrictOnDelete(); // Foreign key ke jobdesks
            $table->string('pangkat', 50)->nullable(); // Junior, Senior, Lead, Manager, Director, dll
            $table->date('hire_date')->nullable(); // Optional nanti kalau bingung delete aja
            $table->enum('status_karyawan', ['aktif', 'non_aktif', 'cuti'])->default('aktif'); 


            // Default Laravel fields
            $table->string('nama');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

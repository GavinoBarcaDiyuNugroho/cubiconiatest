<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['nama' => 'Admin', 'description' => 'Administrator with full access debug dll', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Boss', 'description' => 'Client butuh lihat performa perusahaan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pegawai', 'description' => 'Regular user with limited access', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

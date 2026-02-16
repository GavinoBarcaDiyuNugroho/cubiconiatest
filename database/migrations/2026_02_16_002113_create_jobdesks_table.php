<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobdesks', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('spesialisasi', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('jobdesks')->insert([
            [
                'nama' => 'Backend Developer',
                'spesialisasi' => 'PHP/Laravel',
                'description' => 'Mengembangkan sistem backend',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'Frontend Developer',
                'spesialisasi' => 'React/TypeScript',
                'description' => 'Mengembangkan antarmuka pengguna',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'DevOps Engineer',
                'spesialisasi' => 'Infrastructure',
                'description' => 'Mengelola infrastruktur dan deployment',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'Project Manager',
                'spesialisasi' => 'Management',
                'description' => 'Mengelola proyek dan tim',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Tambahkan beberapa jobdesk lagi jika diperlukan pending untuk frontendnya
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobdesks');
    }
};

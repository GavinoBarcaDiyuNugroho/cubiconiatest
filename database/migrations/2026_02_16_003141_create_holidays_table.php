<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->string('nama', 100);
            $table->boolean('is_national')->default(true); // Untuk membedakan antara hari libur nasional dan perusahaan
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index('tanggal');
        });
        // Seed some 2024 Indonesian holidays
        DB::table('holidays')->insert([
            ['tanggal' => '2024-01-01', 'nama' => 'Tahun Baru Masehi', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-02-08', 'nama' => 'Isra Mikraj', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-02-10', 'nama' => 'Tahun Baru Imlek', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-03-11', 'nama' => 'Hari Raya Nyepi', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-03-29', 'nama' => 'Wafat Isa Almasih', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-04-10', 'nama' => 'Hari Raya Idul Fitri', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-04-11', 'nama' => 'Hari Raya Idul Fitri', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-05-01', 'nama' => 'Hari Buruh', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-05-09', 'nama' => 'Kenaikan Isa Almasih', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-05-23', 'nama' => 'Hari Raya Waisak', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-06-01', 'nama' => 'Hari Lahir Pancasila', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-06-17', 'nama' => 'Hari Raya Idul Adha', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-07-07', 'nama' => 'Tahun Baru Islam', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-08-17', 'nama' => 'Hari Kemerdekaan RI', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-09-16', 'nama' => 'Maulid Nabi Muhammad', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2024-12-25', 'nama' => 'Hari Raya Natal', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed some 2025 Indonesian holidays
        DB::table('holidays')->insert([
            ['tanggal' => '2025-01-01', 'nama' => 'Tahun Baru Masehi', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-01-27', 'nama' => 'Isra Mikraj Nabi Muhammad SAW', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-01-29', 'nama' => 'Tahun Baru Imlek 2576 Kongzili', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-03-29', 'nama' => 'Hari Suci Nyepi Tahun Baru Saka 1947', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-03-31', 'nama' => 'Hari Raya Idul Fitri 1446 Hijriah', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-04-01', 'nama' => 'Hari Raya Idul Fitri 1446 Hijriah', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-04-18', 'nama' => 'Wafat Yesus Kristus', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-04-20', 'nama' => 'Hari Kebangkitan Yesus Kristus (Paskah)', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-05-01', 'nama' => 'Hari Buruh Internasional', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-05-12', 'nama' => 'Hari Raya Waisak 2569 BE', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-05-29', 'nama' => 'Kenaikan Yesus Kristus', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-06-01', 'nama' => 'Hari Lahir Pancasila', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-06-07', 'nama' => 'Hari Raya Idul Adha 1446 Hijriah', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-06-27', 'nama' => 'Tahun Baru Islam 1447 Hijriah', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-08-17', 'nama' => 'Hari Kemerdekaan Republik Indonesia', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-09-05', 'nama' => 'Maulid Nabi Muhammad SAW', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2025-12-25', 'nama' => 'Hari Raya Natal', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed some 2026 Indonesian holidays
        DB::table('holidays')->insert([
            ['tanggal' => '2026-01-01', 'nama' => 'Tahun Baru Masehi', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-01-16', 'nama' => 'Isra Mikraj Nabi Muhammad SAW', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-02-17', 'nama' => 'Tahun Baru Imlek 2577 Kongzili', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-03-19', 'nama' => 'Hari Suci Nyepi Tahun Baru Saka 1948', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-03-20', 'nama' => 'Hari Raya Idul Fitri 1447 Hijriah', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-03-21', 'nama' => 'Hari Raya Idul Fitri 1447 Hijriah', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-04-03', 'nama' => 'Wafat Yesus Kristus', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-04-05', 'nama' => 'Hari Kebangkitan Yesus Kristus (Paskah)', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-05-01', 'nama' => 'Hari Buruh Internasional', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-05-14', 'nama' => 'Kenaikan Yesus Kristus', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-05-27', 'nama' => 'Hari Raya Idul Adha 1447 Hijriah', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-05-31', 'nama' => 'Hari Raya Waisak 2570 BE', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-06-01', 'nama' => 'Hari Lahir Pancasila', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-06-16', 'nama' => 'Tahun Baru Islam 1448 Hijriah', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-08-17', 'nama' => 'Hari Kemerdekaan Republik Indonesia', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-08-26', 'nama' => 'Maulid Nabi Muhammad SAW', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-12-25', 'nama' => 'Hari Raya Natal', 'is_national' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};

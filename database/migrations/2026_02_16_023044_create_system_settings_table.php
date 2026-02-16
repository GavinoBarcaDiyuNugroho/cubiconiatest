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
        Schema::create('system-settings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value');
            $table->text('description')->nullable();

        });

        DB::table('system-settings')->insert([
            [
                'setting_key' => 'company_name',
                'setting_value' => 'GapiNodeOperator',
                'description' => 'Nama perusahaan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'work_hours_start',
                'setting_value' => '09:00:00',
                'description' => 'Jam mulai kerja default',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'work_hours_end',
                'setting_value' => '17:00:00',
                'description' => 'Jam selesai kerja default',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'max_late_minutes',
                'setting_value' => '15',
                'description' => 'Maksimal terlambat (menit)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [ 
                'setting_key' => 'annual_leave_quota',
                'setting_value' => '12',
                'description' => 'Jatah cuti tahunan (hari)',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system-settings');
    }
};

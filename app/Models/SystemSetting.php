<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
    ];

    // Cache settings for performance
    protected static function booted(){
        static::saved(function () {
            Cache::forget('system_settings');
        });

        static::deleted(function () {
            Cache::forget('system_settings');
        });
    }

    // Get setting value with cache
    public static function get(string $key, $default = null){
        $settings = Cache::remember('system_settings', 60, function () {
            return self::pluck('setting_value', 'setting_key')->toArray();
        });
        return $settings[$key] ?? $default;
    }

    // set setting value
    public static function set(string $key, $value, ?string $description = null): void
    {
        self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'description' => $description,
            ]
        );
    }

    // get all settings as array
    public static function getAll(): array{
        return Cache::remember('system_settings', 60, function () {
            return self::pluck('setting_value', 'setting_key')->toArray();
        });
    }

    // setter dan getter khusus untuk setting umum
    public static function getCompanyName(): string
    {
        return self::get('company_name', 'CubiConia GapiNode Operator');
    }
    // jam jam kerja default, bisa diubah di settings
    public static function getWorkHoursStart(): string
    {
        return self::get('work_hours_start', '09:00:00');
    }
    public function getWorkHoursEnd(): string
    {
        return self::get('work_hours_end', '17:00:00');
    }

    public static function getMaxLateMinutes(): int
    {
        return (int) self::get('max_late_minutes', 15);
    }

    // Lom ada dbnya
    // public static function getOvertimeRate(): float
    // {
    //     return (float) self::get('overtime_rate', 1.5);
    // }

    public static function getAnnualLeaveQuota(): int
    {
        return (int) self::get('annual_leave_quota', 12);
    }
}

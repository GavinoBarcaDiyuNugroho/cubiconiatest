<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'nama',
        'tanggal',
        'is_national',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'is_national' => 'boolean',
        ];
    }

    // rentang waktu
    public function scopeInMonth($query, $year, $month)
    {
        return $query->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);
    }
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }
    public function scopeNational($query)
    {
        return $query->where('is_national', true);
    }

    // boolean return 
    public static function isHoliday($date): bool
    {
        return self::where('tanggal', $date)->exists();
    }
    // setter getter biasa
    public static function getHolidaysInRange(Carbon $startDate, Carbon $endDate): array
    {
        return self::whereBetween('tanggal', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        ])->pluck('tanggal')->toArray();
    }
}

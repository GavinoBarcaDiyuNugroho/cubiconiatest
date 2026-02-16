<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SystemSetting;
use Carbon\Carbon;


/**
 * @property float $total_jam_kerja
 * @property-read \Carbon\Carbon $tanggal
 */
class Attendance extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'user_id',
        'tanggal',
        'check_in',
        'check_out',
        'total_jam_kerja',
        'status',
        'keterangan',
        'photo_check_in',
        'location_check_in', // check lagi kalau perlu atau enggak
    ];
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'check_in' => 'datetime:H:i:s',
            'check_out' => 'datetime:H:i:s',
            'total_jam_kerja' => 'decimal:2',
        ];
    }

    // elquent relationships
    public function user(){

        return $this->belongsTo(User::class);
    }

    // fungsi buat di dashboard buat nampilin data kehadiran per bulan
    public function scopeInMonth($query, $year, $month)
    {
        return $query->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }   

    // fungsi buat nampilin data kehadiran yang statusnya hadir aja 
    public function scopePresent($query)
    {
        return $query->where('status', 'hadir');
    }

    // cari pegawai 
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // cari pegawai yang sedang login 
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    // mutator buat format waktu check in jadi H:i:s aja, biar gampang pas diitung total jam kerjanya

    public function setCheckInAttribute($value)
    {
        $this->attributes['check_in'] = Carbon::parse($value)->format('H:i:s');
    }

    public function setCheckOutAttribute($value)
    {
        $this->attributes['check_out'] = Carbon::parse($value)->format('H:i:s');
    }

    // method buat ngitung total jam kerja, dipanggil nanti di controller pas update check out, jadi otomatis keitung total jam kerjanya
    public function calculateWorkHours(): void
     {
        if ($this->check_in && $this->check_out) {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            $this->total_jam_kerja = $checkIn->diffInHours($checkOut);
        }
     }
     
    // method buat cek telat atau enggak, misal check in lebih dari jam 9 pagi, berarti telat
    public function isLate(): bool
    {
        if ($this->check_in) {
            $workStartTime = Carbon::parse(
            SystemSetting::get('work_hours_start', '09:00:00')
        );
        $checkInTime = Carbon::parse($this->check_in);
        $maxLateMinutes = (int) SystemSetting::get('max_late_minutes', 15);

        return $checkInTime->diffInMinutes($workStartTime, false) > $maxLateMinutes;
        }
        return false;
    }

    // foto
    public function getPhotoCheckInUrlAttribute()
    {
        return $this->photo_check_in ? asset('storage/' . $this->photo_check_in) : null;
    }

    // Buat dipake
    public const STATUS_HADIR = 'hadir';
    public const STATUS_SAKIT = 'sakit';
    public const STATUS_IZIN = 'izin';
    public const STATUS_ALPHA = 'alpha';
    public const STATUS_LIBUR = 'libur';
    public const STATUS_CUTI = 'cuti';

}

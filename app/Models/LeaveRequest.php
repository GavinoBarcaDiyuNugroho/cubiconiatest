<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'user_id',
        'tipe',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_hari',
        'alasan',
        'dokumen_pendukung',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'approved_at' => 'datetime',
            'total_hari' => 'integer',
        ];
    }
    // orm

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    // rentang
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('tanggal_mulai', '<=', $startDate)
                        ->where('tanggal_selesai', '>=', $endDate);
                });
        });
    }

    public function approve(User $approver)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }
    public function reject(User $approver, string $reason)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
    // bool for between tunggu pesan balasan ngeleg kepalaku
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    protected function createAttendanceRecords(): void
    {
        $startDate = Carbon::parse($this->tanggal_mulai);
        $endDate = Carbon::parse($this->tanggal_selesai);

        $status = match($this->tipe) {
            'cuti' => Attendance::STATUS_CUTI,
            'izin' => Attendance::STATUS_IZIN,
            'sakit' => Attendance::STATUS_SAKIT,
            default => Attendance::STATUS_IZIN,
        };

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            // Skip holidays
            if (Holiday::isHoliday($date)) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'user_id' => $this->user_id,
                    'tanggal' => $date->format('Y-m-d'),
                ],
                [
                    'status' => $status,
                    'keterangan' => $this->alasan,
                ]
            );
        }
    }
    // surat izin, cuti, sakit dll
    public function getDokumenUrlAttribute(): ?string
        {
            if (!$this->dokumen_pendukung) {
                return null;
            }

            return asset('storage/' . $this->dokumen_pendukung);
        }



    public const TIPE_CUTI = 'cuti';
    public const TIPE_IZIN = 'izin';
    public const TIPE_SAKIT = 'sakit';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    
}

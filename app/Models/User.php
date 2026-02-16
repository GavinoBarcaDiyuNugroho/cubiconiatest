<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'nik_npwp',
        'phone',
        'nikah',
        'jumlah_keluarga',
        'kartu_keluarga_path',
        'photo_path',
        'role_id',
        'jobdesk_id',
        'pangkat',
        'hire_date',
        'status_karyawan',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'hire_date' => 'date',
            'jumlah_keluarga' => 'integer',
        ];
    }

    // orm relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function jobdesk()
    {
        return $this->belongsTo(Jobdesk::class);
    }
    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }
    public function currentSalary()
    {
        return $this->hasOne(Salary::class)->latestOfMany('effective_from');
    }

    public function attendances(){
        return $this->hasMany(Attendance::class);
    }
    public function leaveRequests(){
        return $this->hasMany(LeaveRequest::class); 
    }

    public function approvedLeaveRequests(){
        return $this->hasMany(LeaveRequest::class, 'approved_by');
    }

    public function auditLogs(){
        return $this->hasMany(AuditLog::class);
    }
    public function isAdmin(): bool
    {
        return $this->role->nama === 'admin';
    }

    public function isBoss(): bool
    {
        return $this->role->nama === 'boss';
    }

    public function isPegawai(): bool
    {
        return $this->role->nama === 'pegawai';
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role->nama === $roleName;
    }

    public function canApproveLeave(): bool
    {
        return $this->isAdmin() || $this->isBoss();
    }

    // Accessor for full photo URL
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path 
            ? asset('storage/' . $this->photo_path) 
            : null;
    }

    // Accessor for full KK URL
    public function getKartuKeluargaUrlAttribute(): ?string
    {
        return $this->kartu_keluarga_path 
            ? asset('storage/' . $this->kartu_keluarga_path) 
            : null;
    }
}

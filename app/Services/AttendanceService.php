<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceService
{
    /**
     * Check in user
     */
    public function checkIn(User $user, ?string $photoPath = null, ?string $location = null): Attendance
    {
        $today = Carbon::today();

        // Check if already checked in today
        $attendance = Attendance::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if ($attendance && $attendance->check_in) {
            throw new \Exception('Anda sudah melakukan check-in hari ini');
        }

        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'tanggal' => $today,
            ],
            [
                'check_in' => now(),
                'status' => Attendance::STATUS_HADIR,
                'photo_check_in' => $photoPath,
                'location_check_in' => $location,
            ]
        );

        return $attendance;
    }

    /**
     * Check out user
     */
    public function checkOut(User $user): Attendance
    {
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            throw new \Exception('Anda belum melakukan check-in hari ini');
        }

        if ($attendance->check_out) {
            throw new \Exception('Anda sudah melakukan check-out hari ini');
        }

        $attendance->check_out = now();
        $attendance->calculateWorkHours();
        $attendance->save();

        return $attendance;
    }

    /**
     * Get monthly attendance summary for a user
     */
    public function getMonthlyReport(User $user, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get all attendances for the month
        $attendances = Attendance::where('user_id', $user->id)
            ->inDateRange($startDate, $endDate)
            ->get()
            ->keyBy(fn($a) => $a->tanggal->format('Y-m-d'));

        // Calculate working days (exclude weekends and holidays)
        $workingDays = $this->calculateWorkingDays($startDate, $endDate);
        
        // Count attendance by status
        $statusCounts = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'cuti' => 0,
            'alpha' => 0,
            'libur' => 0,
        ];

        $totalWorkHours = 0;

        foreach ($attendances as $attendance) {
            $statusCounts[$attendance->status]++;
            
            if ($attendance->total_jam_kerja) {
                $totalWorkHours += $attendance->total_jam_kerja;
            }
        }

        // Calculate absent days
        $recordedDays = count($attendances);
        $absentDays = $workingDays - $statusCounts['hadir'] - $statusCounts['sakit'] 
                     - $statusCounts['izin'] - $statusCounts['cuti'];

        return [
            'user' => $user,
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_days' => $endDate->day,
                'working_days' => $workingDays,
                'present_days' => $statusCounts['hadir'],
                'absent_days' => $absentDays,
                'sick_days' => $statusCounts['sakit'],
                'leave_days' => $statusCounts['cuti'],
                'permission_days' => $statusCounts['izin'],
                'total_work_hours' => round($totalWorkHours, 2),
                'attendance_rate' => $workingDays > 0 
                    ? round(($statusCounts['hadir'] / $workingDays) * 100, 2) 
                    : 0,
            ],
            'details' => $attendances->values(),
        ];
    }

    /**
     * Calculate working days in a date range (excluding weekends and holidays)
     */
    public function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $workingDays = 0;
        $holidays = Holiday::getHolidaysInRange($startDate, $endDate);

        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            // Skip holidays
            if (in_array($date->format('Y-m-d'), $holidays)) {
                continue;
            }

            $workingDays++;
        }

        return $workingDays;
    }

    /**
     * Get attendance statistics for multiple users (for boss/admin)
     */
    public function getTeamReport(array $userIds, int $year, int $month): array
    {
        $reports = [];

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $reports[] = $this->getMonthlyReport($user, $year, $month);
            }
        }

        return $reports;
    }

    /**
     * Check if user can check in (not on weekend or holiday)
     */
    public function canCheckIn(Carbon $date = null): bool
    {
        $date = $date ?? Carbon::today();

        // Check if weekend
        if ($date->isWeekend()) {
            return false;
        }

        // Check if holiday
        if (Holiday::isHoliday($date)) {
            return false;
        }

        return true;
    }

    /**
     * Get today's attendance for user
     */
    public function getTodayAttendance(User $user): ?Attendance
    {
        return Attendance::where('user_id', $user->id)
            ->where('tanggal', Carbon::today())
            ->first();
    }
}

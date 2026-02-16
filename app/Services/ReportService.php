<?php

namespace App\Services;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Services\AttendanceService;

class ReportService
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Generate comprehensive monthly report for boss/admin
     */
    public function generateMonthlyReport(int $year, int $month, ?int $jobdeskId = null): array
    {
        $query = User::with(['role', 'jobdesk', 'currentSalary'])
            ->where('status_karyawan', 'aktif');

        if ($jobdeskId) {
            $query->where('jobdesk_id', $jobdeskId);
        }

        $users = $query->get();

        $reports = [];
        $totalSalary = 0;
        $overallStats = [
            'total_employees' => $users->count(),
            'total_present_days' => 0,
            'total_absent_days' => 0,
            'average_attendance_rate' => 0,
        ];

        /** @var User $user */
        foreach ($users as $user) {
            $monthlyReport = $this->attendanceService->getMonthlyReport($user, $year, $month);
            
            $currentSalary = $user->currentSalary;
            $salaryAmount = $currentSalary ? $currentSalary->amount : 0;
            
            $reports[] = [
                'user' => [
                    'id' => $user->id,
                    'nik_npwp' => $user->nik_npwp,
                    'nama' => $user->nama,
                    'jobdesk' => $user->jobdesk?->nama,
                    'pangkat' => $user->pangkat,
                ],
                'attendance' => $monthlyReport['summary'],
                'salary' => [
                    'amount' => $salaryAmount,
                    'formatted' => $currentSalary ? $currentSalary->formatted_amount : 'Rp 0',
                ],
            ];

            $totalSalary += $salaryAmount;
            $overallStats['total_present_days'] += $monthlyReport['summary']['present_days'];
            $overallStats['total_absent_days'] += $monthlyReport['summary']['absent_days'];
        }

        $overallStats['total_salary'] = $totalSalary;
        $overallStats['formatted_total_salary'] = 'Rp ' . number_format($totalSalary, 0, ',', '.');
        
        if ($users->count() > 0) {
            $overallStats['average_attendance_rate'] = round(
                collect($reports)->avg('attendance.attendance_rate'), 
                2
            );
        }

        return [
            'period' => [
                'year' => $year,
                'month' => $month,
                'month_name' => Carbon::create($year, $month)->format('F Y'),
            ],
            'overall_statistics' => $overallStats,
            'employee_reports' => $reports,
        ];
    }

    /**
     * Generate yearly summary report
     */
    public function generateYearlyReport(int $year, ?int $jobdeskId = null): array
    {
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthlyReport = $this->generateMonthlyReport($year, $month, $jobdeskId);
            $monthlyData[] = [
                'month' => $month,
                'month_name' => Carbon::create($year, $month)->format('F'),
                'statistics' => $monthlyReport['overall_statistics'],
            ];
        }

        return [
            'year' => $year,
            'monthly_data' => $monthlyData,
            'yearly_totals' => [
                'total_salary_paid' => collect($monthlyData)->sum('statistics.total_salary'),
                'average_monthly_attendance_rate' => round(
                    collect($monthlyData)->avg('statistics.average_attendance_rate'),
                    2
                ),
            ],
        ];
    }

    /**
     * Generate individual employee detailed report
     */
    public function generateEmployeeDetailReport(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = Attendance::where('user_id', $user->id)
            ->inDateRange($startDate, $endDate)
            ->orderBy('tanggal')
            ->get();

        $summary = [
            'total_days' => $startDate->diffInDays($endDate) + 1,
            'working_days' => $this->attendanceService->calculateWorkingDays($startDate, $endDate),
            'present' => $attendances->where('status', 'hadir')->count(),
            'sick' => $attendances->where('status', 'sakit')->count(),
            'leave' => $attendances->where('status', 'cuti')->count(),
            'permission' => $attendances->where('status', 'izin')->count(),
            'absent' => $attendances->where('status', 'alpha')->count(),
            'total_hours' => round($attendances->sum('total_jam_kerja'), 2),
            'late_days' => $attendances->filter(fn($a) => $a->isLate())->count(),
        ];

        return [
            'user' => [
                'id' => $user->id,
                'nik_npwp' => $user->nik_npwp,
                'nama' => $user->nama,
                'email' => $user->email,
                'jobdesk' => $user->jobdesk?->nama,
                'pangkat' => $user->pangkat,
                'hire_date' => $user->hire_date ? Carbon::parse($user->hire_date)->format('Y-m-d') : null,
            ],
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => $summary,
            'attendance_details' => $attendances->map(function (Attendance $attendance) {
                return [
                    'tanggal' => Carbon::parse($attendance->tanggal)->format('Y-m-d'),
                    'day_name' => Carbon::parse($attendance->tanggal)->format('l'),
                    'check_in' => $attendance->check_in?->format('H:i:s'),
                    'check_out' => $attendance->check_out?->format('H:i:s'),
                    'total_hours' => $attendance->total_jam_kerja,
                    'status' => $attendance->status,
                    'is_late' => $attendance->isLate(),
                    'keterangan' => $attendance->keterangan,
                ];
            }),
        ];
    }

    /**
     * Get salary report (grouped by jobdesk or overall)
     */
    public function getSalaryReport(?int $jobdeskId = null): array
    {
        $query = User::with(['jobdesk', 'currentSalary'])
            ->where('status_karyawan', 'aktif');

        if ($jobdeskId) {
            $query->where('jobdesk_id', $jobdeskId);
        }

        $users = $query->get();

        $salaryData = $users->map(function ($user) {
            $currentSalary = $user->currentSalary;
            return [
                'user_id' => $user->id,
                'nama' => $user->nama,
                'jobdesk' => $user->jobdesk?->nama,
                'pangkat' => $user->pangkat,
                'salary' => $currentSalary ? $currentSalary->amount : 0,
                'formatted_salary' => $currentSalary ? $currentSalary->formatted_amount : 'Rp 0',
            ];
        });

        $groupedByJobdesk = $salaryData->groupBy('jobdesk')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_salary' => $group->sum('salary'),
                'average_salary' => round($group->avg('salary'), 2),
                'employees' => $group->values(),
            ];
        });

        return [
            'total_employees' => $users->count(),
            'total_salary' => $salaryData->sum('salary'),
            'formatted_total_salary' => 'Rp ' . number_format($salaryData->sum('salary'), 0, ',', '.'),
            'average_salary' => round($salaryData->avg('salary'), 2),
            'by_jobdesk' => $groupedByJobdesk,
        ];
    }
}

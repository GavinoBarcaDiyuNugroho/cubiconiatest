<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Show dashboard based on user role
     */
    public function index(Request $request)
    {
        $user = $request->user()->load(['role', 'jobdesk', 'currentSalary']);

        if ($user->isPegawai()) {
            return $this->pegawaiDashboard($user);
        } elseif ($user->isBoss() || $user->isAdmin()) {
            return $this->bossDashboard($user);
        }

        return Inertia::render('Dashboard');
    }

    /**
     * Pegawai Dashboard
     */
    protected function pegawaiDashboard(User $user)
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now();

        // Today's attendance
        $todayAttendance = $this->attendanceService->getTodayAttendance($user);

        // Monthly report
        $monthlyReport = $this->attendanceService->getMonthlyReport(
            $user,
            $currentMonth->year,
            $currentMonth->month
        );

        // Pending leave requests count
        $pendingLeaves = LeaveRequest::where('user_id', $user->id)
            ->pending()
            ->count();

        return Inertia::render('Dashboard', [
            'user' => $user,
            'role' => 'pegawai',
            'today' => [
                'date' => $today->format('Y-m-d'),
                'day_name' => $today->format('l'),
                'attendance' => $todayAttendance,
                'can_check_in' => $this->attendanceService->canCheckIn(),
            ],
            'monthlyStats' => $monthlyReport['summary'],
            'pendingLeaves' => $pendingLeaves,
        ]);
    }

    /**
     * Boss/Admin Dashboard
     */
    protected function bossDashboard(User $user)
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now();

        // Total active employees
        $totalEmployees = User::where('status_karyawan', 'aktif')
            ->count();

        // Today's attendance
        $todayAttendances = Attendance::whereDate('tanggal', $today)->get();
        
        $todayStats = [
            'present' => $todayAttendances->where('status', 'hadir')->count(),
            'sick' => $todayAttendances->where('status', 'sakit')->count(),
            'leave' => $todayAttendances->where('status', 'cuti')->count(),
            'absent' => $totalEmployees - $todayAttendances->count(),
        ];

        // Pending leave requests
        $pendingLeaveRequests = LeaveRequest::with(['user'])
            ->pending()
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        // This month's attendance rate
        $monthStart = $currentMonth->copy()->startOfMonth();
        $monthEnd = $currentMonth->copy()->endOfMonth();
        $workingDays = $this->attendanceService->calculateWorkingDays($monthStart, $monthEnd);
        
        $monthlyPresentCount = Attendance::whereYear('tanggal', $currentMonth->year)
            ->whereMonth('tanggal', $currentMonth->month)
            ->where('status', 'hadir')
            ->count();

        $expectedAttendances = $totalEmployees * $workingDays;
        $attendanceRate = $expectedAttendances > 0 
            ? round(($monthlyPresentCount / $expectedAttendances) * 100, 2) 
            : 0;

        return Inertia::render('Dashboard', [
            'user' => $user,
            'role' => 'boss',
            'overview' => [
                'total_employees' => $totalEmployees,
                'monthly_attendance_rate' => $attendanceRate,
                'pending_leave_requests' => $pendingLeaveRequests->count(),
            ],
            'todayStats' => $todayStats,
            'pendingLeaves' => $pendingLeaveRequests,
        ]);
    }
}

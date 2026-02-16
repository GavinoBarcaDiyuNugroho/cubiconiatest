<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Show attendance page with today's status and history
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Today's attendance
        $todayAttendance = $this->attendanceService->getTodayAttendance($user);
        $canCheckIn = $this->attendanceService->canCheckIn();
        
        // Current month report
        $currentMonth = Carbon::now();
        $monthlyReport = $this->attendanceService->getMonthlyReport(
            $user,
            $currentMonth->year,
            $currentMonth->month
        );
        
        // Recent history (last 30 days)
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                Carbon::today()->subDays(29),
                Carbon::today()
            ])
            ->orderBy('tanggal', 'desc')
            ->get();

        return Inertia::render('Attendance/Index', [
            'todayAttendance' => $todayAttendance,
            'canCheckIn' => $canCheckIn,
            'monthlyReport' => $monthlyReport,
            'recentAttendances' => $recentAttendances,
        ]);
    }

    /**
     * Check in
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'photo' => 'sometimes|image|max:2048',
            'location' => 'sometimes|string',
        ]);

        if (!$this->attendanceService->canCheckIn()) {
            return back()->with('error', 'Cannot check-in on weekends or holidays');
        }

        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('attendance-photos', 'public');
            }

            $attendance = $this->attendanceService->checkIn(
                $request->user(),
                $photoPath,
                $request->location
            );

            return back()->with('success', 'Check-in successful!');
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Check out
     */
    public function checkOut(Request $request)
    {
        try {
            $attendance = $this->attendanceService->checkOut($request->user());
            return back()->with('success', 'Check-out successful!');
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

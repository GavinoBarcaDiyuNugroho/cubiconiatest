<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Show leave requests page
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // My leave requests
        $myLeaveRequests = LeaveRequest::where('user_id', $user->id)
            ->with('approvedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Pending requests (if boss/admin)
        $pendingRequests = null;
        if ($user->canApproveLeave()) {
            $pendingRequests = LeaveRequest::with(['user.jobdesk'])
                ->pending()
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return Inertia::render('Leave/Index', [
            'myLeaveRequests' => $myLeaveRequests,
            'pendingRequests' => $pendingRequests,
            'canApprove' => $user->canApproveLeave(),
        ]);
    }

    /**
     * Store new leave request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe' => 'required|in:cuti,izin,sakit',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'dokumen_pendukung' => 'sometimes|file|mimes:pdf,jpg,png|max:5120',
        ]);

        // Calculate working days
        $startDate = Carbon::parse($validated['tanggal_mulai']);
        $endDate = Carbon::parse($validated['tanggal_selesai']);
        $totalHari = $this->attendanceService->calculateWorkingDays($startDate, $endDate);

        if ($totalHari === 0) {
            return back()->with('error', 'No working days in the selected date range');
        }

        // Handle document upload
        if ($request->hasFile('dokumen_pendukung')) {
            $validated['dokumen_pendukung'] = $request->file('dokumen_pendukung')
                ->store('leave-documents', 'public');
        }

        $validated['user_id'] = $request->user()->id;
        $validated['total_hari'] = $totalHari;

        LeaveRequest::create($validated);

        return back()->with('success', 'Leave request submitted successfully');
    }

    /**
     * Approve leave request
     */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        if (!$request->user()->canApproveLeave()) {
            abort(403, 'Unauthorized');
        }

        if (!$leaveRequest->isPending()) {
            return back()->with('error', 'Leave request has already been processed');
        }

        $leaveRequest->approve($request->user());

        return back()->with('success', 'Leave request approved');
    }

    /**
     * Reject leave request
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if (!$request->user()->canApproveLeave()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        if (!$leaveRequest->isPending()) {
            return back()->with('error', 'Leave request has already been processed');
        }

        $leaveRequest->reject($request->user(), $validated['rejection_reason']);

        return back()->with('success', 'Leave request rejected');
    }

    /**
     * Cancel own leave request
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!$leaveRequest->isPending()) {
            return back()->with('error', 'Only pending leave requests can be cancelled');
        }

        $leaveRequest->delete();

        return back()->with('success', 'Leave request cancelled');
    }
}

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageProps } from '';

interface DashboardProps extends PageProps {
    role?: 'pegawai' | 'boss';
    today?: {
        date: string;
        day_name: string;
        attendance: any;
        can_check_in: boolean;
    };
    monthlyStats?: {
        working_days: number;
        present_days: number;
        absent_days: number;
        attendance_rate: number;
    };
    pendingLeaves?: number;
    overview?: {
        total_employees: number;
        monthly_attendance_rate: number;
        pending_leave_requests: number;
    };
    todayStats?: {
        present: number;
        sick: number;
        leave: number;
        absent: number;
    };
}

export default function Dashboard({ 
    auth, 
    role,
    today,
    monthlyStats,
    pendingLeaves,
    overview,
    todayStats 
}: DashboardProps) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    
                    {/* Welcome Message */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div className="p-6 text-gray-900">
                            <h3 className="text-2xl font-bold">Welcome, {auth.user.name}!</h3>
                            <p className="text-gray-600 mt-1">
                                {today?.day_name}, {today?.date}
                            </p>
                        </div>
                    </div>

                    {/* Pegawai Dashboard */}
                    {role === 'pegawai' && (
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {/* Today's Status */}
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6">
                                    <h3 className="text-lg font-semibold mb-4">Today's Attendance</h3>
                                    
                                    {today?.attendance ? (
                                        <div className="space-y-2">
                                            <p className="text-sm text-gray-600">
                                                Check-in: <span className="font-semibold">{today.attendance.check_in || '-'}</span>
                                            </p>
                                            <p className="text-sm text-gray-600">
                                                Check-out: <span className="font-semibold">{today.attendance.check_out || '-'}</span>
                                            </p>
                                            <p className="text-sm text-gray-600">
                                                Status: <span className="font-semibold capitalize">{today.attendance.status}</span>
                                            </p>
                                        </div>
                                    ) : (
                                        <p className="text-sm text-gray-600">No attendance record yet</p>
                                    )}

                                    <div className="mt-4">
                                        <a 
                                            href="/attendance" 
                                            className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                        >
                                            Go to Attendance →
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {/* Monthly Stats */}
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6">
                                    <h3 className="text-lg font-semibold mb-4">This Month</h3>
                                    
                                    {monthlyStats && (
                                        <div className="space-y-2">
                                            <div className="flex justify-between">
                                                <span className="text-sm text-gray-600">Working Days:</span>
                                                <span className="font-semibold">{monthlyStats.working_days}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-gray-600">Present:</span>
                                                <span className="font-semibold text-green-600">{monthlyStats.present_days}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-gray-600">Absent:</span>
                                                <span className="font-semibold text-red-600">{monthlyStats.absent_days}</span>
                                            </div>
                                            <div className="flex justify-between pt-2 border-t">
                                                <span className="text-sm font-semibold">Attendance Rate:</span>
                                                <span className="font-bold text-blue-600">{monthlyStats.attendance_rate}%</span>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Leave Requests */}
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6">
                                    <h3 className="text-lg font-semibold mb-4">Leave Requests</h3>
                                    <p className="text-sm text-gray-600 mb-4">
                                        You have <span className="font-semibold">{pendingLeaves}</span> pending request(s)
                                    </p>
                                    <a 
                                        href="/leave-requests" 
                                        className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                    >
                                        Manage Leave Requests →
                                    </a>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Boss Dashboard */}
                    {role === 'boss' && (
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            {/* Overview Cards */}
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6">
                                    <h4 className="text-sm text-gray-600 mb-2">Total Employees</h4>
                                    <p className="text-3xl font-bold">{overview?.total_employees}</p>
                                </div>
                            </div>

                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6">
                                    <h4 className="text-sm text-gray-600 mb-2">Monthly Attendance</h4>
                                    <p className="text-3xl font-bold text-blue-600">
                                        {overview?.monthly_attendance_rate}%
                                    </p>
                                </div>
                            </div>

                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6">
                                    <h4 className="text-sm text-gray-600 mb-2">Pending Requests</h4>
                                    <p className="text-3xl font-bold text-orange-600">
                                        {overview?.pending_leave_requests}
                                    </p>
                                </div>
                            </div>

                            {/* Today's Attendance */}
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg md:col-span-3">
                                <div className="p-6">
                                    <h3 className="text-lg font-semibold mb-4">Today's Attendance</h3>
                                    
                                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div className="text-center p-4 bg-green-50 rounded">
                                            <p className="text-2xl font-bold text-green-600">{todayStats?.present}</p>
                                            <p className="text-sm text-gray-600">Present</p>
                                        </div>
                                        <div className="text-center p-4 bg-yellow-50 rounded">
                                            <p className="text-2xl font-bold text-yellow-600">{todayStats?.sick}</p>
                                            <p className="text-sm text-gray-600">Sick</p>
                                        </div>
                                        <div className="text-center p-4 bg-blue-50 rounded">
                                            <p className="text-2xl font-bold text-blue-600">{todayStats?.leave}</p>
                                            <p className="text-sm text-gray-600">Leave</p>
                                        </div>
                                        <div className="text-center p-4 bg-red-50 rounded">
                                            <p className="text-2xl font-bold text-red-600">{todayStats?.absent}</p>
                                            <p className="text-sm text-gray-600">Absent</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Quick Links */}
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg md:col-span-3">
                                <div className="p-6">
                                    <h3 className="text-lg font-semibold mb-4">Quick Actions</h3>
                                    <div className="flex gap-4">
                                        <a 
                                            href="/leave-requests" 
                                            className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        >
                                            Review Leave Requests
                                        </a>
                                        <a 
                                            href="/reports" 
                                            className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
                                        >
                                            View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

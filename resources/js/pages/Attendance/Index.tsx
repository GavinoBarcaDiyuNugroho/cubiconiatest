import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageProps } from '@/types';
import { FormEventHandler } from 'react';

interface AttendanceProps extends PageProps {
    todayAttendance: any;
    canCheckIn: boolean;
    monthlyReport: {
        summary: {
            working_days: number;
            present_days: number;
            absent_days: number;
            sick_days: number;
            leave_days: number;
            total_work_hours: number;
            attendance_rate: number;
        };
    };
    recentAttendances: Array<{
        id: number;
        tanggal: string;
        check_in: string | null;
        check_out: string | null;
        status: string;
        total_jam_kerja: number | null;
    }>;
}

export default function AttendanceIndex({ 
    auth, 
    todayAttendance,
    canCheckIn,
    monthlyReport,
    recentAttendances 
}: AttendanceProps) {
    
    const { post, processing } = useForm();

    const handleCheckIn: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('attendance.check-in'));
    };

    const handleCheckOut: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('attendance.check-out'));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Attendance</h2>}
        >
            <Head title="Attendance" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    
                    {/* Check In/Out Section */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h3 className="text-lg font-semibold mb-4">Today's Attendance</h3>
                            
                            {todayAttendance ? (
                                <div className="space-y-4">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <p className="text-sm text-gray-600">Check-in</p>
                                            <p className="text-xl font-semibold">
                                                {todayAttendance.check_in || '-'}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-gray-600">Check-out</p>
                                            <p className="text-xl font-semibold">
                                                {todayAttendance.check_out || '-'}
                                            </p>
                                        </div>
                                    </div>

                                    {!todayAttendance.check_out && (
                                        <button
                                            onClick={handleCheckOut}
                                            disabled={processing}
                                            className="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
                                        >
                                            {processing ? 'Processing...' : 'Check Out'}
                                        </button>
                                    )}
                                </div>
                            ) : canCheckIn ? (
                                <div>
                                    <p className="text-sm text-gray-600 mb-4">
                                        You haven't checked in today
                                    </p>
                                    <button
                                        onClick={handleCheckIn}
                                        disabled={processing}
                                        className="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
                                    >
                                        {processing ? 'Processing...' : 'Check In'}
                                    </button>
                                </div>
                            ) : (
                                <div className="text-center py-4">
                                    <p className="text-gray-600">
                                        Cannot check-in today (Weekend or Holiday)
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Monthly Report */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h3 className="text-lg font-semibold mb-4">This Month's Summary</h3>
                            
                            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div className="text-center p-4 bg-gray-50 rounded">
                                    <p className="text-2xl font-bold">{monthlyReport.summary.working_days}</p>
                                    <p className="text-sm text-gray-600">Working Days</p>
                                </div>
                                <div className="text-center p-4 bg-green-50 rounded">
                                    <p className="text-2xl font-bold text-green-600">{monthlyReport.summary.present_days}</p>
                                    <p className="text-sm text-gray-600">Present</p>
                                </div>
                                <div className="text-center p-4 bg-red-50 rounded">
                                    <p className="text-2xl font-bold text-red-600">{monthlyReport.summary.absent_days}</p>
                                    <p className="text-sm text-gray-600">Absent</p>
                                </div>
                                <div className="text-center p-4 bg-blue-50 rounded">
                                    <p className="text-2xl font-bold text-blue-600">{monthlyReport.summary.attendance_rate}%</p>
                                    <p className="text-sm text-gray-600">Rate</p>
                                </div>
                            </div>

                            <div className="mt-4 pt-4 border-t">
                                <div className="grid grid-cols-3 gap-4 text-sm">
                                    <div className="text-center">
                                        <p className="font-semibold">{monthlyReport.summary.sick_days}</p>
                                        <p className="text-gray-600">Sick Days</p>
                                    </div>
                                    <div className="text-center">
                                        <p className="font-semibold">{monthlyReport.summary.leave_days}</p>
                                        <p className="text-gray-600">Leave Days</p>
                                    </div>
                                    <div className="text-center">
                                        <p className="font-semibold">{monthlyReport.summary.total_work_hours}h</p>
                                        <p className="text-gray-600">Total Hours</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Recent History */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h3 className="text-lg font-semibold mb-4">Recent Attendance History</h3>
                            
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check Out</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {recentAttendances.map((attendance) => (
                                            <tr key={attendance.id}>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm">
                                                    {new Date(attendance.tanggal).toLocaleDateString()}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm">
                                                    {attendance.check_in || '-'}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm">
                                                    {attendance.check_out || '-'}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm">
                                                    {attendance.total_jam_kerja || '-'}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        ${attendance.status === 'hadir' ? 'bg-green-100 text-green-800' : 
                                                          attendance.status === 'sakit' ? 'bg-yellow-100 text-yellow-800' : 
                                                          attendance.status === 'cuti' ? 'bg-blue-100 text-blue-800' : 
                                                          'bg-red-100 text-red-800'}`}>
                                                        {attendance.status}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { PageProps } from '@/types';
import { FormEventHandler, useState } from 'react';

interface LeaveRequest {
    id: number;
    tipe: string;
    tanggal_mulai: string;
    tanggal_selesai: string;
    total_hari: number;
    alasan: string;
    status: string;
    approved_by?: {
        nama: string;
    };
    rejection_reason?: string;
}

interface LeaveProps extends PageProps {
    myLeaveRequests: {
        data: LeaveRequest[];
    };
    pendingRequests?: Array<LeaveRequest & {
        user: {
            nama: string;
            jobdesk: { nama: string };
        };
    }>;
    canApprove: boolean;
}

export default function LeaveIndex({ 
    auth, 
    myLeaveRequests,
    pendingRequests,
    canApprove 
}: LeaveProps) {
    
    const [showForm, setShowForm] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        tipe: 'cuti',
        tanggal_mulai: '',
        tanggal_selesai: '',
        alasan: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('leave-requests.store'), {
            onSuccess: () => {
                reset();
                setShowForm(false);
            }
        });
    };

    const handleApprove = (id: number) => {
        post(route('leave-requests.approve', id));
    };

    const handleReject = (id: number, reason: string) => {
        post(route('leave-requests.reject', id), {
            data: { rejection_reason: reason }
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Leave Requests</h2>}
        >
            <Head title="Leave Requests" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    
                    {/* Submit New Request */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-4">
                                <h3 className="text-lg font-semibold">Request Leave</h3>
                                <button
                                    onClick={() => setShowForm(!showForm)}
                                    className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                >
                                    {showForm ? 'Cancel' : 'New Request'}
                                </button>
                            </div>

                            {showForm && (
                                <form onSubmit={submit} className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">Type</label>
                                        <select
                                            value={data.tipe}
                                            onChange={e => setData('tipe', e.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        >
                                            <option value="cuti">Cuti (Annual Leave)</option>
                                            <option value="izin">Izin (Permission)</option>
                                            <option value="sakit">Sakit (Sick Leave)</option>
                                        </select>
                                    </div>

                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Start Date</label>
                                            <input
                                                type="date"
                                                value={data.tanggal_mulai}
                                                onChange={e => setData('tanggal_mulai', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                required
                                            />
                                            {errors.tanggal_mulai && <p className="text-red-600 text-sm mt-1">{errors.tanggal_mulai}</p>}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">End Date</label>
                                            <input
                                                type="date"
                                                value={data.tanggal_selesai}
                                                onChange={e => setData('tanggal_selesai', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                required
                                            />
                                            {errors.tanggal_selesai && <p className="text-red-600 text-sm mt-1">{errors.tanggal_selesai}</p>}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">Reason</label>
                                        <textarea
                                            value={data.alasan}
                                            onChange={e => setData('alasan', e.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                            rows={3}
                                            required
                                        />
                                        {errors.alasan && <p className="text-red-600 text-sm mt-1">{errors.alasan}</p>}
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
                                    >
                                        {processing ? 'Submitting...' : 'Submit Request'}
                                    </button>
                                </form>
                            )}
                        </div>
                    </div>

                    {/* Pending Requests (Boss/Admin) */}
                    {canApprove && pendingRequests && pendingRequests.length > 0 && (
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <h3 className="text-lg font-semibold mb-4">Pending Approvals</h3>
                                
                                <div className="space-y-4">
                                    {pendingRequests.map((request) => (
                                        <div key={request.id} className="border rounded-lg p-4">
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <p className="font-semibold">{request.user.nama}</p>
                                                    <p className="text-sm text-gray-600">{request.user.jobdesk.nama}</p>
                                                    <p className="text-sm text-gray-600 mt-2">
                                                        <span className="capitalize font-medium">{request.tipe}</span>
                                                        {' '}- {request.total_hari} day(s)
                                                    </p>
                                                    <p className="text-sm text-gray-600">
                                                        {new Date(request.tanggal_mulai).toLocaleDateString()} - {new Date(request.tanggal_selesai).toLocaleDateString()}
                                                    </p>
                                                    <p className="text-sm mt-2">{request.alasan}</p>
                                                </div>
                                                <div className="flex gap-2">
                                                    <button
                                                        onClick={() => handleApprove(request.id)}
                                                        disabled={processing}
                                                        className="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm"
                                                    >
                                                        Approve
                                                    </button>
                                                    <button
                                                        onClick={() => {
                                                            const reason = prompt('Rejection reason:');
                                                            if (reason) handleReject(request.id, reason);
                                                        }}
                                                        disabled={processing}
                                                        className="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm"
                                                    >
                                                        Reject
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}

                    {/* My Requests */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h3 className="text-lg font-semibold mb-4">My Leave Requests</h3>
                            
                            <div className="space-y-4">
                                {myLeaveRequests.data.map((request) => (
                                    <div key={request.id} className="border rounded-lg p-4">
                                        <div className="flex justify-between items-start">
                                            <div>
                                                <p className="font-semibold capitalize">{request.tipe}</p>
                                                <p className="text-sm text-gray-600">
                                                    {new Date(request.tanggal_mulai).toLocaleDateString()} - {new Date(request.tanggal_selesai).toLocaleDateString()}
                                                </p>
                                                <p className="text-sm text-gray-600">{request.total_hari} day(s)</p>
                                                <p className="text-sm mt-2">{request.alasan}</p>
                                                
                                                {request.status === 'approved' && request.approved_by && (
                                                    <p className="text-sm text-green-600 mt-2">
                                                        Approved by: {request.approved_by.nama}
                                                    </p>
                                                )}
                                                
                                                {request.status === 'rejected' && request.rejection_reason && (
                                                    <p className="text-sm text-red-600 mt-2">
                                                        Rejected: {request.rejection_reason}
                                                    </p>
                                                )}
                                            </div>
                                            <span className={`px-3 py-1 text-xs font-semibold rounded-full
                                                ${request.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                  request.status === 'approved' ? 'bg-green-100 text-green-800' :
                                                  'bg-red-100 text-red-800'}`}>
                                                {request.status}
                                            </span>
                                        </div>
                                    </div>
                                ))}
                                
                                {myLeaveRequests.data.length === 0 && (
                                    <p className="text-gray-600 text-center py-4">No leave requests yet</p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

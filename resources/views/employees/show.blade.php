@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Employee Details</h2>
                        <p class="mt-1 text-sm text-gray-600">View employee information</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('employees.edit', $employee) }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Employee
                        </a>
                        <a href="{{ route('employees.index') }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow-sm transition duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to List
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- User Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Password Management</label>
                                <dd class="mt-1">
                                    <form action="{{ route('employees.reset-password', $employee) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Are you sure you want to reset the password?')"
                                            class="inline-flex items-center px-3 py-1.5 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:border-yellow-700 focus:ring focus:ring-yellow-200 active:bg-yellow-600 disabled:opacity-25 transition">
                                            Reset Password
                                        </button>
                                    </form>
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Employee ID</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->employee_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Department</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->department }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Position</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $employee->position }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <span class="mt-1 inline-flex px-3 py-1 text-xs leading-5 font-semibold rounded-full 
                                    {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($employee->status === 'on_leave' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance History -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Attendance</h3>
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Time In</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Time Out</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($employee->attendances()->latest()->take(5)->get() as $attendance)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $attendance->time_in->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $attendance->time_in->format('h:i A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $attendance->time_out ? $attendance->time_out->format('h:i A') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                                       ($attendance->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No attendance records found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
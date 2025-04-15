@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Attendance</h2>
                        <p class="mt-1 text-sm text-gray-600">Record your daily attendance</p>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-bold text-gray-900" id="current-time">00:00:00</div>
                        <div class="text-sm text-gray-500" id="current-date">January 1, 2024</div>
                    </div>
                </div>

                <div id="alert-message" class="hidden mb-6"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Today's Status -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Today's Status</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Time In</label>
                                <p class="mt-1 text-sm text-gray-900" id="time-in">Not yet</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Time Out</label>
                                <p class="mt-1 text-sm text-gray-900" id="time-out">Not yet</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <span class="mt-1 inline-flex px-3 py-1 text-xs leading-5 font-semibold rounded-full" id="attendance-status">
                                    <span class="status-text">Not Recorded</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-4">
                            <button type="button" 
                                    id="time-in-btn"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Time In
                            </button>

                            <button type="button"
                                    id="time-out-btn"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Time Out
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Attendance History -->
                <div class="bg-white rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Attendance History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="attendance-history">
                                <!-- Attendance history will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Update current time
    function updateClock() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString();
        document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Update attendance status
    function updateAttendanceStatus() {
        fetch('{{ route("attendance.status") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('time-in').textContent = data.time_in || 'Not yet';
                document.getElementById('time-out').textContent = data.time_out || 'Not yet';
                
                const statusElement = document.getElementById('attendance-status');
                const statusText = document.querySelector('#attendance-status .status-text');
                statusText.textContent = data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'Not Recorded';
                
                // Update status color
                statusElement.className = 'mt-1 inline-flex px-3 py-1 text-xs leading-5 font-semibold rounded-full';
                if (data.status === 'present') {
                    statusElement.classList.add('bg-green-100', 'text-green-800');
                } else if (data.status === 'late') {
                    statusElement.classList.add('bg-yellow-100', 'text-yellow-800');
                } else {
                    statusElement.classList.add('bg-gray-100', 'text-gray-800');
                }

                // Update attendance history
                const historyBody = document.getElementById('attendance-history');
                historyBody.innerHTML = data.attendances.map(attendance => `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${attendance.date}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${attendance.time_in}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${attendance.time_out || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${attendance.status === 'present' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                ${attendance.status.charAt(0).toUpperCase() + attendance.status.slice(1)}
                            </span>
                        </td>
                    </tr>
                `).join('');
            });
    }

    // Time In button click handler
    document.getElementById('time-in-btn').addEventListener('click', function() {
        fetch('{{ route("attendance.timeIn") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                updateAttendanceStatus();
            }
        });
    });

    // Time Out button click handler
    document.getElementById('time-out-btn').addEventListener('click', function() {
        fetch('{{ route("attendance.timeOut") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                updateAttendanceStatus();
            }
        });
    });

    // Show alert message
    function showAlert(message, type) {
        const alertDiv = document.getElementById('alert-message');
        alertDiv.className = `mb-6 p-4 rounded-lg ${type === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'}`;
        alertDiv.textContent = message;
        alertDiv.classList.remove('hidden');
        
        setTimeout(() => {
            alertDiv.classList.add('hidden');
        }, 5000);
    }

    // Initial status update
    updateAttendanceStatus();
    // Update status every 30 seconds
    setInterval(updateAttendanceStatus, 30000);
</script>
@endpush
@endsection 
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Manage Your Employees Efficiently</h2>
                    <p class="mt-2 text-lg text-gray-600">A comprehensive solution for managing your workforce</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Employee Management Card -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6 text-center">
                            <i class="fas fa-users text-4xl text-blue-500 mb-4"></i>
                            <h5 class="text-lg font-semibold text-gray-900 mb-2">Employee Management</h5>
                            <p class="text-gray-600">Add, edit, and manage employee information</p>
                        </div>
                    </div>

                    <!-- Attendance Tracking Card -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6 text-center">
                            <i class="fas fa-calendar-alt text-4xl text-green-500 mb-4"></i>
                            <h5 class="text-lg font-semibold text-gray-900 mb-2">Attendance Tracking</h5>
                            <p class="text-gray-600">Monitor employee attendance and leaves</p>
                        </div>
                    </div>

                    <!-- Reports & Analytics Card -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6 text-center">
                            <i class="fas fa-chart-bar text-4xl text-blue-400 mb-4"></i>
                            <h5 class="text-lg font-semibold text-gray-900 mb-2">Reports & Analytics</h5>
                            <p class="text-gray-600">Generate reports and analyze employee data</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
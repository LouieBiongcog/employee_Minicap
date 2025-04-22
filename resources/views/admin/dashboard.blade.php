@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Admin Dashboard</h5>
                </div>

                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Employees</h5>
                                    <h2 class="mb-0">{{ $totalEmployees }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Present Today</h5>
                                    <h2 class="mb-0">{{ $presentCount }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Late Today</h5>
                                    <h2 class="mb-0">{{ $lateCount }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Absent Today</h5>
                                    <h2 class="mb-0">{{ $absentCount }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Quick Actions</h5>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('employees.index') }}" class="btn btn-primary">
                                            <i class="fas fa-users"></i> Manage Employees
                                        </a>
                                        <a href="{{ route('admin.attendance') }}" class="btn btn-info">
                                            <i class="fas fa-calendar-check"></i> View Attendance
                                        </a>
                                        <a href="{{ route('admin.attendance.today') }}" class="btn btn-success">
                                            <i class="fas fa-clock"></i> Today's Attendance
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Recent Activity</h5>
                                    <!-- Add recent activity content here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
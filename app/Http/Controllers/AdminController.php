<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\AdminMiddleware::class);
    }

    public function dashboard()
    {
        $today = now()->format('Y-m-d');
        
        // Get all employees
        $employees = User::where('is_admin', false)->get();
        $totalEmployees = $employees->count();

        // Get today's attendance
        $todayAttendance = Attendance::whereDate('date', $today)->get();
        
        // Count attendance status
        $presentCount = $todayAttendance->where('status', 'present')->count();
        $lateCount = $todayAttendance->where('status', 'late')->count();
        $absentCount = $totalEmployees - $todayAttendance->count();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'presentCount',
            'lateCount',
            'absentCount'
        ));
    }

    public function attendance()
    {
        $employees = User::with(['attendance' => function($query) {
            $query->orderBy('date', 'desc');
        }])->get();

        return view('admin.attendance.index', compact('employees'));
    }

    public function todayAttendance()
    {
        $today = now()->format('Y-m-d');
        $employees = User::with(['attendance' => function($query) use ($today) {
            $query->whereDate('date', $today);
        }])->get();

        return view('admin.attendance.today', compact('employees', 'today'));
    }

    public function employeeAttendance(User $employee)
    {
        $attendance = $employee->attendance()->orderBy('date', 'desc')->paginate(30);
        return view('admin.attendance.employee', compact('employee', 'attendance'));
    }
} 
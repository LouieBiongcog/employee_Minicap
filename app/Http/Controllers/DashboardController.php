<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Get all employees
        $employees = Employee::with(['user', 'attendances' => function($query) use ($today) {
            $query->whereDate('time_in', $today);
        }])->get();

        // Count attendance status
        $presentCount = 0;
        $lateCount = 0;
        $absentCount = 0;

        foreach ($employees as $employee) {
            $attendance = $employee->attendances->first();
            if ($attendance) {
                if ($attendance->status === 'late') {
                    $lateCount++;
                } else {
                    $presentCount++;
                }
            } else {
                $absentCount++;
            }
        }

        return view('dashboard', compact('employees', 'presentCount', 'lateCount', 'absentCount'));
    }
} 
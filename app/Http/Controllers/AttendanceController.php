<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        
        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Your employee profile is not set up. Please contact HR to complete your employee profile setup.');
        }

        $now = Carbon::now('Asia/Manila');
        
        // Get today's attendance
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('time_in', $now->toDateString())
            ->first();

        // Get attendance history
        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('time_in', 'desc')
            ->take(10)
            ->get();

        return view('attendance.index', compact('attendances', 'todayAttendance'));
    }

    public function timeIn()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee profile is not set up. Please contact HR to complete your employee profile setup.'
            ], 403);
        }

        $now = Carbon::now('Asia/Manila');
        
        // Check if user already timed in today
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('time_in', $now->toDateString())
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'You have already timed in today.'
            ]);
        }

        // Determine if late (after 9:00 AM)
        $isLate = $now->hour >= 9 && $now->minute > 0;

        try {
            // Create new attendance record
            $attendance = new Attendance();
            $attendance->employee_id = $employee->id;
            $attendance->time_in = $now;
            $attendance->status = $isLate ? 'late' : 'present';
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Time in recorded successfully.',
                'data' => [
                    'time_in' => $attendance->time_in->format('h:i A'),
                    'status' => $attendance->status,
                    'date' => $attendance->time_in->format('Y-m-d')
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Time in error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record time in. Please try again.'
            ], 500);
        }
    }

    public function timeOut()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee profile is not set up. Please contact HR to complete your employee profile setup.'
            ], 403);
        }

        $now = Carbon::now('Asia/Manila');
        
        // Find today's attendance record
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('time_in', $now->toDateString())
            ->whereNull('time_out')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No active attendance record found. Please time in first.'
            ]);
        }

        try {
            // Update the attendance record with time out
            $attendance->time_out = $now;
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Time out recorded successfully.',
                'data' => [
                    'time_out' => $attendance->time_out->format('h:i A'),
                    'status' => $attendance->status,
                    'date' => $attendance->time_in->format('Y-m-d')
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Time out error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record time out. Please try again.'
            ], 500);
        }
    }

    public function status()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee profile is not set up. Please contact HR to complete your employee profile setup.'
            ], 403);
        }

        $now = Carbon::now('Asia/Manila');
        
        // Get today's attendance
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('time_in', $now->toDateString())
            ->first();

        // Get recent attendance history
        $attendances = Attendance::where('employee_id', $employee->id)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'time_in' => $todayAttendance ? $todayAttendance->time_in->format('h:i A') : null,
            'time_out' => $todayAttendance && $todayAttendance->time_out ? $todayAttendance->time_out->format('h:i A') : null,
            'status' => $todayAttendance ? $todayAttendance->status : null,
            'attendances' => $attendances->map(function ($attendance) {
                return [
                    'date' => $attendance->time_in->format('Y-m-d'),
                    'time_in' => $attendance->time_in->format('h:i A'),
                    'time_out' => $attendance->time_out ? $attendance->time_out->format('h:i A') : null,
                    'status' => $attendance->status
                ];
            })
        ]);
    }

    public function today()
    {
        $today = Carbon::now('Asia/Manila')->format('Y-m-d');
        $employees = Employee::with(['user', 'attendances' => function($query) use ($today) {
            $query->whereDate('time_in', $today);
        }])->get();

        return view('attendance.today', compact('employees'));
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Get today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('time_in', $now->toDateString())
            ->first();

        // Get attendance history
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('time_in', 'desc')
            ->take(10)
            ->get();

        return view('attendance.index', compact('attendances', 'todayAttendance'));
    }

    public function timeIn()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Check if user already timed in today
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('time_in', $now->toDateString())
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'You have already timed in today.');
        }

        // Create new attendance record
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'time_in' => $now,
            'status' => $now->hour >= 9 ? 'late' : 'present', // Consider late if after 9 AM
        ]);

        return redirect()->back()->with('success', 'Time in recorded successfully.');
    }

    public function timeOut()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Find today's attendance record
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('time_in', $now->toDateString())
            ->whereNull('time_out')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'No active attendance record found. Please time in first.');
        }

        if ($attendance->time_out) {
            return redirect()->back()->with('error', 'You have already timed out today.');
        }

        // Update the attendance record with time out
        $attendance->update([
            'time_out' => $now,
        ]);

        return redirect()->back()->with('success', 'Time out recorded successfully.');
    }

    public function status()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Get today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('time_in', $now->toDateString())
            ->first();

        // Get recent attendance history
        $attendances = Attendance::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'time_in' => $todayAttendance ? $todayAttendance->time_in->format('h:i A') : null,
            'time_out' => $todayAttendance && $todayAttendance->time_out ? $todayAttendance->time_out->format('h:i A') : null,
            'status' => $todayAttendance ? $todayAttendance->status : null,
            'attendances' => $attendances->map(function ($attendance) {
                return [
                    'time_in' => $attendance->time_in,
                    'time_out' => $attendance->time_out,
                    'status' => $attendance->status
                ];
            })
        ]);
    }
} 
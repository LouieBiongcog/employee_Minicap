<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\AdminMiddleware::class);
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:attendance,employee',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        if ($request->report_type === 'attendance') {
            $data = $this->generateAttendanceReport($startDate, $endDate);
            $view = 'admin.reports.attendance';
        } else {
            $data = $this->generateEmployeeReport($startDate, $endDate);
            $view = 'admin.reports.employee';
        }

        $pdf = Pdf::loadView($view, $data);
        return $pdf->download('report-' . $request->report_type . '-' . now()->format('Y-m-d') . '.pdf');
    }

    private function generateAttendanceReport($startDate, $endDate)
    {
        $attendances = Attendance::with('employee.user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

            $totalDays = $endDate->diffInDays($startDate) + 1;
            $totalPresent = $attendances->where('status', 'present')->count();
            $totalLate = $attendances->where('status', 'late')->count();
            $totalAbsent = $totalDays - ($totalPresent + $totalLate);
            
            $summary = [
                'total_days' => $totalDays,
                'total_present' => $totalPresent,
                'total_late' => $totalLate,
                'total_absent' => $totalAbsent,
            ];
            

        return [
            'attendances' => $attendances,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalDays' => $summary['total_days'],
            'presentDays' => $summary['total_present'],
            'lateDays' => $summary['total_late'],
            'absentDays' => $summary['total_absent']
        ];
    }

    private function generateEmployeeReport($startDate, $endDate)
    {
        $employees = User::where('is_admin', false)
            ->with(['employee.attendances' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }])
            ->get();

        $employeeStats = $employees->map(function($user) use ($startDate, $endDate) {
            $attendances = $user->employee->attendances;
            return [
                'name' => $user->name,
                'email' => $user->email,
                'total_days' => $endDate->diffInDays($startDate) + 1,
                'present_days' => $attendances->where('status', 'present')->count(),
                'late_days' => $attendances->where('status', 'late')->count(),
                'absent_days' => ($endDate->diffInDays($startDate) + 1) - $attendances->count(),
                'attendance_rate' => round(($attendances->count() / ($endDate->diffInDays($startDate) + 1)) * 100, 2),
            ];
        });

        return [
            'employees' => $employeeStats,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }
} 
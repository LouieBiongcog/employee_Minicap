<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_employees' => Employee::getTotalEmployees(),
            'present_today' => Employee::getPresentToday(),
            'on_leave' => Employee::getOnLeave(),
        ];

        return view('dashboard', compact('stats'));
    }
} 
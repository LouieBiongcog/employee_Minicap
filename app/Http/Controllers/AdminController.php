<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function dashboard()
    {
        // Get the authenticated user
        $user = Auth::user();

        // TODO: Add actual data fetching for:
        // - Total employees count
        // - Present today count
        // - On leave count
        // - Recent activities

        return view('admin.dashboard');
    }
} 
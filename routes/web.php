<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Auth\SetPasswordController;

Route::get('/', function () {
    return view('home');
})->name('home');

// Password setting routes (accessible without auth)
Route::get('/password/set', [SetPasswordController::class, 'showSetPasswordForm'])->name('password.set.form');
Route::post('/password/set', [SetPasswordController::class, 'setPassword'])->name('password.set');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Logout route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
    
    // Admin routes
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('employees', EmployeeController::class);
        Route::post('/employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');
    });
    
    // Employee routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/today', [AttendanceController::class, 'today'])->name('attendance.today');
    Route::post('/attendance/time-in', [AttendanceController::class, 'timeIn'])->name('attendance.timeIn');
    Route::post('/attendance/time-out', [AttendanceController::class, 'timeOut'])->name('attendance.timeOut');
    Route::get('/attendance/status', [AttendanceController::class, 'status'])->name('attendance.status');
});

// Admin Routes
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// Route::get('/show-time', function () {
//     // Get the current time in Manila
//     $nowManila = Carbon::now('Asia/Manila');

//     // Pass the current time to the view
//     return view('show-time', ['nowManila' => $nowManila]);
// })->name('showTime');

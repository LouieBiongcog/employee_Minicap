<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'department',
        'position',
        'hire_date',
        'salary',
        'phone',
        'address',
        'status',
    ];

    protected $casts = [
        'hire_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Get total number of employees
    public static function getTotalEmployees()
    {
        return self::count();
    }

    // Get number of employees present today
    public static function getPresentToday()
    {
        return self::whereHas('attendances', function($query) {
            $query->whereDate('time_in', Carbon::today())
                  ->whereNotNull('time_in')
                  ->whereNull('time_out');
        })->count();
    }

    // Get number of employees on leave
    public static function getOnLeave()
    {
        return self::where('status', 'on_leave')->count();
    }
}

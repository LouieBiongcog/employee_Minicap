<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $departments = ['IT', 'HR', 'Finance', 'Marketing', 'Sales', 'Operations'];
        $positions = ['Manager', 'Senior', 'Junior', 'Associate', 'Director', 'Coordinator'];
        
        for ($i = 1; $i <= 30; $i++) {
            // Create user account for each employee
            $user = User::create([
                'name' => "Employee $i",
                'email' => "employee$i@example.com",
                'password' => Hash::make('password'),
            ]);

            // Create employee record
            Employee::create([
                'user_id' => $user->id,
                'employee_id' => 'EMP' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'department' => $departments[array_rand($departments)],
                'position' => $positions[array_rand($positions)],
                'hire_date' => now()->subDays(rand(1, 365)),
                'salary' => rand(30000, 120000),
                'phone' => '09' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'address' => "Address $i, City",
                'status' => 'active',
            ]);
        }
    }
} 
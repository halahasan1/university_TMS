<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Computer Science',
            'Electrical Engineering',
            'Mechanical Engineering',
            'Civil Engineering',
            'Business Administration',
            'Mathematics',
            'Physics',
            'Arts & Humanities',
        ];

        foreach ($names as $name) {
            Department::firstOrCreate(['name' => $name]);
        }
    }
}

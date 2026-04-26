<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Faculty;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $engineering = Faculty::where('name', 'Faculty of Engineering')->first();
        $medicine    = Faculty::where('name', 'Faculty of Medicine')->first();
        $science     = Faculty::where('name', 'Faculty of Science')->first();

        $departments = [
            ['name' => 'Computer Engineering', 'faculty_id' => $engineering->id],
            ['name' => 'Electrical Engineering', 'faculty_id' => $engineering->id],
            ['name' => 'Mechanical Engineering', 'faculty_id' => $engineering->id],
            ['name' => 'General Medicine', 'faculty_id' => $medicine->id],
            ['name' => 'Dentistry', 'faculty_id' => $medicine->id],
            ['name' => 'Physics', 'faculty_id' => $science->id],
            ['name' => 'Chemistry', 'faculty_id' => $science->id],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}

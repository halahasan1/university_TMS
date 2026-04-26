<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\User;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();
        $years = AcademicYear::all();
        $doctor = User::first(); // Any user as default owner

        foreach ($departments as $dept) {
            foreach ($years as $year) {
                Course::create([
                    'department_id'    => $dept->id,
                    'academic_year_id' => $year->id,
                    'name'             => "Course $dept->name - Year {$year->year_number}",
                    'code'             => strtoupper(substr($dept->name, 0, 3)) . rand(100, 999),
                    'description'      => 'Sample description for course.',
                    'owner_id'         => $doctor?->id,
                ]);
            }
        }
    }
}

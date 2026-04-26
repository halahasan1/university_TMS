<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Course;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::take(10)->get();

        foreach ($courses as $course) {
            Exam::create([
                'course_id'   => $course->id,
                'title'       => 'Practice Exam: ' . $course->name,
                'description' => 'Sample practice exam.',
                'is_practice' => true,
                'start_time'  => now(),
                'end_time'    => now()->addHours(2),
            ]);
        }
    }
}

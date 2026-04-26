<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;

class ExamAttemptSeeder extends Seeder
{
    public function run(): void
    {
        $exam = Exam::first();
        $student = User::first();

        if ($exam && $student) {
            ExamAttempt::create([
                'exam_id'     => $exam->id,
                'student_id'  => $student->id,
                'started_at'  => now(),
                'finished_at' => now()->addMinutes(20),
                'score'       => 85.5,
            ]);
        }
    }
}

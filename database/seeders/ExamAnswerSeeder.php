<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\ExamAnswer;

class ExamAnswerSeeder extends Seeder
{
    public function run(): void
    {
        $attempt = ExamAttempt::first();
        $questions = Question::take(5)->get();

        if ($attempt) {
            foreach ($questions as $q) {
                ExamAnswer::create([
                    'exam_attempt_id' => $attempt->id,
                    'question_id'     => $q->id,
                    'answer'          => 'Sample Answer',
                    'is_correct'      => true,
                ]);
            }
        }
    }
}

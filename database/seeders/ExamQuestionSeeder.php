<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamQuestion;

class ExamQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $exams = Exam::all();
        $questions = Question::all();

        foreach ($exams as $exam) {
            $selected = $questions->random( min(5, $questions->count()) );

            foreach ($selected as $q) {
                ExamQuestion::create([
                    'exam_id'     => $exam->id,
                    'question_id' => $q->id,
                    'points'      => 1,
                ]);
            }
        }
    }
}

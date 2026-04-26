<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\User;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $materials = CourseMaterial::all();
        $creator = User::first();

        foreach ($materials as $mat) {
            Question::create([
                'course_id'           => $mat->course_id,
                'course_material_id'  => $mat->id,
                'type'                => 'mcq',
                'text'                => "What is the topic of {$mat->title}?",
                'choices'             => json_encode(['Topic A', 'Topic B', 'Topic C']),
                'correct_answer'      => 'Topic A',
                'difficulty'          => 'easy',
                'source'              => 'manual',
                'status'              => 'approved',
                'created_by'          => $creator?->id,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseMaterial;
use App\Models\Course;
use App\Models\User;

class CourseMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::take(10)->get();
        $uploader = User::first();

        foreach ($courses as $course) {
            CourseMaterial::create([
                'course_id'     => $course->id,
                'title'         => 'Lecture 1: Basics',
                'file_path'     => null, // or storage path
                'extracted_text'=> 'This is sample extracted text for testing RAG.',
                'uploaded_by'   => $uploader?->id,
            ]);

            CourseMaterial::create([
                'course_id'     => $course->id,
                'title'         => 'Lecture 2: Advanced',
                'file_path'     => null,
                'extracted_text'=> 'Another extracted paragraph.',
                'uploaded_by'   => $uploader?->id,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            DepartmentsSeeder::class,
            UserSeeder::class,
            AssignProfilesSeeder::class,
            NewsSeeder::class,
            TaskSeeder::class,
            SubtaskSeeder::class,
            CommentSeeder::class,
            LikeSeeder::class,
            FacultySeeder::class,
            DepartmentSeeder::class,
            AcademicYearSeeder::class,
            CourseSeeder::class,
            CourseMaterialSeeder::class,
            QuestionSeeder::class,
            ExamSeeder::class,
            ExamQuestionSeeder::class,
            ExamAttemptSeeder::class,
            ExamAnswerSeeder::class
        ]);
    }
}

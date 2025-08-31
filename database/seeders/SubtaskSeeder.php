<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;

class SubtaskSeeder extends Seeder
{
    public function run(): void
    {
        $task = Task::where('title','Publish midterm exam schedule')->first();
        if ($task) {
            $task->subtasks()->createMany([
                ['title' => 'Collect dates from instructors', 'done' => false],
                ['title' => 'Book exam halls',               'done' => false],
                ['title' => 'Publish PDF on portal',         'done' => false],
            ]);
        }

        $task = Task::where('title','Lab PC maintenance (CS Lab 2)')->first();
        if ($task) {
            $task->subtasks()->createMany([
                ['title' => 'Update OS image',  'done' => true],
                ['title' => 'Install CUDA',     'done' => false],
                ['title' => 'Run diagnostics',  'done' => false],
            ]);
        }
    }
}

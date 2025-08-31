<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $super    = User::role('super_admin')->first();
        $deans    = User::role('dean')->get();
        $profs    = User::role('professor')->get();
        $students = User::role('student')->get();

        $creators = collect([$super])->filter()->concat($deans)->concat($profs);

        $titlesStaff = [
            'Prepare accreditation documents',
            'Finalize course outlines',
            'Publish exam schedule',
            'Update lab software images',
            'Prepare orientation slides',
            'Department budget review',
            'Research group weekly report',
            'Schedule industry visit',
            'Organize seminar speakers',
            'Update department handbook',
            'Set TA office hours timetable',
            'Prepare internship shortlist',
        ];

        $titlesStudent = [
            'Complete assignment',
            'Prepare lab report',
            'Read chapter and summarize',
            'Solve problem set',
            'Review lecture notes',
            'Prepare presentation',
            'Group meeting notes',
            'Quiz preparation',
        ];

        foreach ($deans->concat($profs) as $assignee) {
            for ($i = 0; $i < 10; $i++) {
                $this->makeTask($titlesStaff[array_rand($titlesStaff)], $assignee, $creators->random());
            }
        }

        foreach ($students as $student) {
            $count = rand(5, 6);
            for ($i = 0; $i < $count; $i++) {
                $this->makeTask($titlesStudent[array_rand($titlesStudent)], $student, $creators->whereIn('id', $profs->pluck('id'))->random());
            }
        }
    }

    private function makeTask(string $baseTitle, User $assignee, User $creator): void
    {
        $priority = collect(['low','medium','high'])->random();
        $due      = Carbon::now()->addDays(rand(-7, 14))->setTime(rand(8,17), [0,15,30,45][array_rand([0,1,2,3])]);

        $status = 'pending';
        if ($due->isPast() && rand(0,1)) $status = 'pending';
        elseif (rand(0,1))              $status = 'in_progress';
        if (rand(0,4) === 0)            $status = 'completed';

        $task = Task::create([
            'title'       => $baseTitle.(rand(0,1) ? '' : ' - '.fake()->words(2, true)),
            'description' => fake()->sentences(rand(1, 2), true),
            'priority'    => $priority,
            'status'      => $status,
            'due_date'    => $due,
            'assigned_to' => $assignee->id,
            'created_by'  => $creator->id,
            'created_at'  => Carbon::now()->subDays(rand(1, 20)),
            'updated_at'  => Carbon::now()->subDays(rand(0, 10)),
        ]);

        $subCount = rand(0, 3);
        for ($s = 0; $s < $subCount; $s++) {
            $task->subtasks()->create([
                'title' => fake()->sentence(3),
                'done'  => (bool) rand(0,1),
            ]);
        }

        if (rand(0,1)) {
            $commenters = User::role('student')->inRandomOrder()->take(rand(1,2))->get();
            foreach ($commenters as $u) {
                $task->comments()->create([
                    'user_id' => $u->id,
                    'body'    => fake()->sentence(8),
                ]);
            }
        }
    }
}

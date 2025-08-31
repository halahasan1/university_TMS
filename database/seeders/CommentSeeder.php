<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\News;
use App\Models\Task;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $dean = User::where('email','dean@university.com')->first();
        $prof = User::where('email','professor@university.com')->first();
        $stud = User::where('email','student@university.com')->first();

        $news = News::where('title','Midterm Exam Schedule Published')->first();
        if ($news && $dean) {
            $news->comments()->create([
                'user_id' => $dean->id,
                'body'    => 'Please double-check room capacities for CS101.',
            ]);
        }

        $task = Task::where('title','Prepare orientation day slides')->first();
        if ($task && $stud) {
            $task->comments()->create([
                'user_id' => $stud->id,
                'body'    => 'I can help with the design and animations.',
            ]);
        }

        if ($news && $prof) {
            $news->comments()->create([
                'user_id' => $prof->id,
                'body'    => 'Added the schedule link to the course pages.',
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\News;
use App\Models\Like;

class LikeSeeder extends Seeder
{
    public function run(): void
    {
        $leader = User::where('email','leader@student.com')->first();
        $stud   = User::where('email','student@university.com')->first();

        $news = News::where('title','Library Extends Opening Hours')->first();

        if ($news && $leader) {
            Like::firstOrCreate([
                'user_id'       => $leader->id,
                'likeable_id'   => $news->id,
                'likeable_type' => News::class,
            ]);
        }

        if ($news) {
            $comment = $news->comments()->first();
            if ($comment && $stud) {
                Like::firstOrCreate([
                    'user_id'       => $stud->id,
                    'likeable_id'   => $comment->id,
                    'likeable_type' => get_class($comment),
                ]);
            }
        }
    }
}

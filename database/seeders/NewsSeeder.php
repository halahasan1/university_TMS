<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use App\Models\Like;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $admins   = User::role('super_admin')->get();
        $deans    = User::role('dean')->get();
        $profs    = User::role('professor')->get();
        $students = User::role('student')->get();

        $authors  = $admins->concat($deans)->concat($profs);

        $payloads = [
            ['t' => 'Fall Semester Registration Opens',
             'b' => 'Registration for the Fall semester opens next Monday. Please review your study plan and meet your advisor if needed.',
             'aud' => 'global',
             'imgs' => []],

            ['t' => 'University Career Fair 2025',
             'b' => 'Top companies are coming to campus. Bring your CV and dress formally.',
             'aud' => 'global',
             'imgs' => ['news-images/career-1.jpg', 'news-images/career-2.jpg']],

            ['t' => 'CS Dept: New AI Lab Hours',
             'b' => 'The AI Research Lab will be open 9AM–7PM during weekdays. Book your slot on the department portal.',
             'aud' => 'department_only',
             'imgs' => []],

            ['t' => 'Midterm Exam Schedule Published',
             'b' => 'The midterm schedule is now live. Check your course pages for rooms.',
             'aud' => 'global',
             'imgs' => []],

            ['t' => 'Student Clubs Day',
             'b' => 'Visit the main yard on Wednesday and discover 30+ student clubs.',
             'aud' => 'global',
             'imgs' => ['news-images/clubs-1.jpg', 'news-images/clubs-2.jpg', 'news-images/clubs-3.jpg']],

            ['t' => 'Library Extends Opening Hours',
             'b' => 'The main library will be open until 11PM during exam period.',
             'aud' => 'global',
             'imgs' => []],
        ];

        foreach ($payloads as $p) {
            $author = $authors->random();
            $news = News::firstOrCreate(
                ['title' => $p['t']],
                [
                    'user_id'     => $author->id,
                    'body'        => $p['b'],
                    'images'      => $p['imgs'],
                    'created_at'  => Carbon::now()->subDays(rand(1, 28)),
                    'updated_at'  => Carbon::now()->subDays(rand(0, 14)),
                ]
            );
            $news->update(['audience_type' => $p['aud']]);

            $commenters = $students->concat($profs)->shuffle()->take(rand(1, 3));
            foreach ($commenters as $u) {
                $c = $news->comments()->create([
                    'user_id' => $u->id,
                    'body'    => fake()->sentences(rand(1, 2), true),
                    'created_at' => Carbon::now()->subDays(rand(0, 10)),
                ]);

                $likers = $students->shuffle()->take(rand(0, 3));
                foreach ($likers as $lk) {
                    Like::firstOrCreate([
                        'user_id'       => $lk->id,
                        'likeable_id'   => $c->id,
                        'likeable_type' => get_class($c),
                    ]);
                }
            }

            $newsLikers = $students->concat($profs)->shuffle()->take(rand(0, 6));
            foreach ($newsLikers as $lk) {
                Like::firstOrCreate([
                    'user_id'       => $lk->id,
                    'likeable_id'   => $news->id,
                    'likeable_type' => News::class,
                ]);
            }
        }
    }
}

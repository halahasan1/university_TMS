<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            'Faculty of Engineering',
            'Faculty of Medicine',
            'Faculty of Science',
            'Faculty of Business Administration',
            'Faculty of Information Technology',
        ];

        foreach ($faculties as $name) {
            Faculty::create(['name' => $name]);
        }
    }
}

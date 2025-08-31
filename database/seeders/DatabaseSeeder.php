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
        ]);
    }
}

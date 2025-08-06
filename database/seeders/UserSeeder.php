<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'super@admin.com',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Dean User',
                'email' => 'dean@university.com',
                'role' => 'dean',
            ],
            [
                'name' => 'Prof. John Doe',
                'email' => 'professor@university.com',
                'role' => 'professor',
            ],
            [
                'name' => 'Student Leader',
                'email' => 'leader@student.com',
                'role' => 'representative_student',
            ],
            [
                'name' => 'Regular Student',
                'email' => 'student@university.com',
                'role' => 'student',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'), 
                ]
            );

            $user->assignRole($userData['role']);
        }
    }
}

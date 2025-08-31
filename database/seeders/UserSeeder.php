<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $fixed = [
            ['name' => 'Super Admin',        'email' => 'super@admin.com',          'role' => 'super_admin',          'dept' => 'Business Administration'],
            ['name' => 'Dean of CS',         'email' => 'dean@university.com',      'role' => 'dean',                 'dept' => 'Computer Science'],
            ['name' => 'Dean of Engineering','email' => 'dean.eng@university.com',  'role' => 'dean',                 'dept' => 'Electrical Engineering'],

            ['name' => 'Prof. John Doe',     'email' => 'prof.john@university.com', 'role' => 'professor',            'dept' => 'Computer Science'],
            ['name' => 'Dr. Sara Haddad',    'email' => 'prof.sara@university.com', 'role' => 'professor',            'dept' => 'Electrical Engineering'],
            ['name' => 'Dr. Omar Nassar',    'email' => 'prof.omar@university.com', 'role' => 'professor',            'dept' => 'Mechanical Engineering'],
            ['name' => 'Dr. Lina Barakat',   'email' => 'prof.lina@university.com', 'role' => 'professor',            'dept' => 'Business Administration'],
            ['name' => 'Dr. Youssef Hamdan', 'email' => 'prof.youssef@university.com', 'role' => 'professor',        'dept' => 'Civil Engineering'],

            ['name' => 'Student Leader',     'email' => 'leader@student.com',       'role' => 'representative_student','dept' => 'Electrical Engineering'],
            ['name' => 'Regular Student',    'email' => 'student@university.com',   'role' => 'student',              'dept' => 'Electrical Engineering'],
        ];

        foreach ($fixed as $row) {
            $user = User::firstOrCreate(
                ['email' => $row['email']],
                ['name' => $row['name'], 'password' => Hash::make('password')]
            );
            $user->assignRole($row['role']);

            $deptId = Department::where('name', $row['dept'])->value('id');
            if ($deptId) {
                $user->profile()->update(['department_id' => $deptId]);
            }
        }

        $allDeptIds = Department::pluck('id')->all();
        for ($i = 1; $i <= 20; $i++) {
            $email = "student{$i}@university.com";
            $u = User::firstOrCreate(
                ['email' => $email],
                ['name' => 'Student '.$i, 'password' => Hash::make('password')]
            );
            $u->assignRole('student');
            $u->profile()->update(['department_id' => $allDeptIds[array_rand($allDeptIds)]]);
        }
    }
}

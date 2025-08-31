<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Department;

class AssignProfilesSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasColumn('profiles', 'department_id')) {
            return;
        }

        $cs  = Department::where('name', 'Computer Science')->first();
        $ee  = Department::where('name', 'Electrical Engineering')->first();
        $bus = Department::where('name', 'Business Administration')->first();

        $map = [
            'super@admin.com'         => $bus?->id,
            'dean@university.com'     => $cs?->id,
            'professor@university.com'=> $cs?->id,
            'leader@student.com'      => $ee?->id,
            'student@university.com'  => $ee?->id,
        ];

        foreach ($map as $email => $deptId) {
            if (! $deptId) continue;

            $user = User::where('email', $email)->first();
            if (! $user) continue;

            $profile = $user->profile;
            $profile->department_id = $deptId;
            $profile->save();
        }
    }
}

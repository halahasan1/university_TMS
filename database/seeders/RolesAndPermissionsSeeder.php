<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            'manage-users',
            'view-users',

            // Task Permissions
            'assign-tasks',
            'view-all-tasks',
            'view-own-tasks',
            
            'comment-on-tasks',

            // News
            'create-news',
            'view-news',

            // Department Management
            'manage-departments',

            // Calendar
            'access-calendar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and their permissions
        $roles = [
            'super_admin' => [
                'manage-users', 'view-users', 'manage-departments',
                'assign-tasks',
                'view-all-tasks',
            ],

            'dean' => [
                'view-users',
                'assign-tasks',
                'view-all-tasks',
            ],

            'professor' => [
                'assign-tasks',
                'view-all-tasks',
            ],

            'representative_student' => [
                'view-own-tasks',
                'comment-on-tasks',
            ],

            'student' => [
                'view-own-tasks',
                'comment-on-tasks',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
}

}


<?php

namespace App\Services;

use App\Models\User;

class UserService
{

    public function find($id)
    {
        return User::with('roles', 'profile')->findOrFail($id);
    }

    public function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $user->assignRole($data['role']);
        return $user;
    }

    public function update($id, array $data)
    {
        $user = $this->find($id);
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
        if (!empty($data['password'])) {
            $user->update(['password' => bcrypt($data['password'])]);
        }

        $user->syncRoles([$data['role']]);
        return $user;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
    
        $user->syncRoles([]); // Removes all assigned roles
    
        $user->delete();
    }
}

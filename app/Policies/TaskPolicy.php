<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{

    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function update(User $user, Task $task): bool
    {
        // if he is the one who create the task
        if ($task->created_by === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->assigned_by === $user->id;
    }

    public function view(User $user, Task $task): bool
    {
        return $task->created_by === $user->id || $task->assigned_to === $user->id;
    }
}

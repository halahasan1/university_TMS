<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class TaskService
{
    /**
     * determine who you can assign task to
     */
    public function getAssignableUsers(User $authUser): Collection
    {
        if ($authUser->hasRole('super_admin')) {
            return User::where('id', '!=', $authUser->id)->get();
        }

        if ($authUser->hasRole('dean')) {
            return User::whereHas('roles', fn($q) =>
                $q->whereNotIn('name', ['super_admin'])
            )->get();
        }

        if ($authUser->hasRole('professor')) {
            return User::whereHas('roles', fn($q) =>
                $q->whereIn(['student', 'representative_student'])
            )->get();
        }

        return collect(); // others cannot assign
    }

    /**
     * filter the tasks that user can see
     */
    public function getTasksForUser(User $user): Collection
    {
        if ($user->hasRole('super_admin')) {
            return Task::with(['assignedTo', 'createdBy'])->latest()->get();
        }

        if ($user->hasRole('dean') || $user->hasRole('professor')) {
            return Task::with(['assignedTo', 'createdBy'])
                ->where(function ($query) use ($user) {
                    $query->where('assigned_to', $user->id)
                          ->orWhere('assigned_by', $user->id);
                })
                ->latest()->get();
        }

        // student or rep
        return Task::with(['assignedTo', 'createdBy'])
            ->where('assigned_to', $user->id)
            ->latest()
            ->get();
    }

    public function createTask(array $data, User $authUser): Task
    {
        return Task::create([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'assigned_by'  => $authUser->id,
            'assigned_to'  => $data['assigned_to'],
            'due_date'     => $data['due_date'] ?? null,
        ]);
    }

    public function updateTask(Task $task, array $data): bool
    {
        return $task->update([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'assigned_to'  => $data['assigned_to'],
            'due_date'     => $data['due_date'] ?? null,
            'status'       => $data['status'] ?? $task->status,
        ]);
    }

    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    public function completeTask(Task $task): bool
    {
        return $task->update(['status' => 'completed']);
    }
}

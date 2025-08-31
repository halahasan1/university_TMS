<?php

namespace App\Observers;

use App\Models\Task;
use App\Support\Notify;

class TaskObserver
{
    public function created(Task $task): void
    {
        Notify::taskAssigned($task);
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged('assigned_to')) {
            $oldId = $task->getOriginal('assigned_to');
            $oldUser = $oldId ? \App\Models\User::find($oldId) : null;
            $newUser = $task->assignedTo;
            Notify::taskReassigned($task, $oldUser, $newUser);
        }
    }
}

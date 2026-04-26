<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Models\Task;
use App\Models\Subtask;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\TaskResource;

class MyTasks extends Page
{
    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.pages.my-tasks';
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'My Tasks';
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return auth()->check() && !auth()->user()->hasRole('super_admin');
    }

    public function getTasksProperty()
    {
        return Task::where('assigned_to', auth()->id())
            ->with('subtasks', 'department', 'createdBy')
            ->orderBy('due_date')
            ->get();
    }

    public function toggleTaskDone($taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->assigned_to !== auth()->id()) {
            abort(403);
        }

        if ($task->status === 'pending') {
            $task->status = 'in_progress';

            if (!$task->started_at) {
                $task->started_at = now();
            }
        } elseif ($task->status === 'in_progress') {
            $task->status = 'in_review';
        } elseif ($task->status === 'in_review') {
            $task->status = 'completed';
            $task->completed_at = now();
        } elseif ($task->status === 'completed') {
            $task->status = 'in_progress';
            $task->completed_at = null;
        }

        $task->save();
    }

    public function toggleSubtask($subtaskId)
    {
        $sub = Subtask::findOrFail($subtaskId);

        if ($sub->task->assigned_to !== auth()->id()) {
            abort(403);
        }

        $sub->done = !$sub->done;
        $sub->save();

        $task = $sub->task;

        if (!$task->started_at) {
            $task->started_at = now();
        }

        if ($task->subtasks()->where('done', false)->doesntExist()) {
            $task->status = 'in_review';
            $task->completed_at = null;
        } elseif ($task->status === 'completed') {
            $task->status = 'in_progress';
            $task->completed_at = null;
        } elseif ($task->status === 'pending') {
            $task->status = 'in_progress';
        }

        $task->save();
    }
}

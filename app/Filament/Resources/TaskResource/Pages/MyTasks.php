<?php

namespace App\Filament\Resources\TaskResource\Pages;


use App\Models\Task;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\TaskResource;

class MyTasks extends Page
{
    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.pages.my-tasks';
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'My Tasks';
    protected static ?int $navigationSort = 3;


    public function getTasksProperty()
    {
        return Task::where('assigned_to', auth()->id())->with('subtasks')->get();
    }

    public function toggleTaskDone($taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->status === 'pending') {
            $task->status = 'in_progress';
        } elseif ($task->status === 'in_progress') {
            $task->status = 'completed';
        } elseif ($task->status === 'completed') {
            $task->status = 'in_progress';
        }

        $task->save();
    }


    public function toggleSubtask($subtaskId)
    {
        $sub = \App\Models\Subtask::findOrFail($subtaskId);
        $sub->done = !$sub->done;
        $sub->save();

        $task = $sub->task;

        if ($task->subtasks()->where('done', false)->doesntExist()) {

            $task->status = 'completed';
            $task->save();
        } elseif ($task->status === 'completed') {

            $task->status = 'in_progress';
            $task->save();
        }
    }
}


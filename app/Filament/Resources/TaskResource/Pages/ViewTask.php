<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Models\Task;
use Filament\Actions;
use App\Models\Subtask;
use App\Filament\Resources\TaskResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.pages.view-task';


    public function getTasksProperty()
    {
        return Task::where('assigned_to' || 'created_by' || hasRole('super_admin'), auth()->id())->with('subtasks')->get();
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
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

        if ($task->subtasks()->where('done', false)->doesntExist()) {
            $task->status = 'completed';
        } elseif ($task->status === 'completed') {
            $task->status = 'in_progress';
        }

        $task->save();
    }
}

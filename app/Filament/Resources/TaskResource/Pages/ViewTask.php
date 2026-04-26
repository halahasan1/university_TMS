<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Subtask;
use App\Models\Task;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.pages.view-task';


    protected function authorizeAccess(): void
    {
        $record = $this->getRecord();
        $user = auth()->user();

        if (
            ! $user->hasRole('super_admin') &&
            (int) $record->created_by !== (int) $user->id &&
            (int) $record->assigned_to !== (int) $user->id
        ) {
            abort(403);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(function () {
                    $user = auth()->user();
                    $record = $this->getRecord();

                    return $user->hasRole('super_admin')
                        || (int) $record->created_by === (int) $user->id;
                }),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->getRecord()->load('subtasks', 'assignedTo.profile', 'createdBy.profile', 'department'),
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

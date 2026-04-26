<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\User;
use App\Support\Notify;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Task created')
            ->success()
            ->send();

        Notify::taskAssigned($this->record);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Task created')
            ->body('Assigned to '.optional($this->record->assignedTo)->name);
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        $assignedUser = User::with('profile')->find($data['assigned_to'] ?? null);
        $data['department_id'] = $assignedUser?->profile?->department_id;

        if (($data['status'] ?? 'pending') === 'in_progress') {
            $data['started_at'] = now();
        }

        if (($data['status'] ?? null) === 'completed') {
            $data['started_at'] = now();
            $data['completed_at'] = now();
        }

        return $data;
    }
}

<?php

namespace App\Filament\Resources\TaskResource\Pages;

use Filament\Actions;
use App\Support\Notify;
use App\Filament\Resources\TaskResource;
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
}
